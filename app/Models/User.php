<?php

namespace App\Models;

use App\Exceptions\InsufficientException;
use App\Exceptions\InvalidTransferException;
use App\Exceptions\PaymentServiceInavailableException;
use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'document', // CPF/CNPJ
        'logist',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'balance' => 'float',
        'logist' => 'boolean',
    ];

    /**
     * Has many transactions.
     *
     * @return Builder
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class)
            ->orWhere(function (Builder $query) {
                return $query->where('to_user_id', '=', $this->id);
            })
            ->orderBy('date', 'desc');
    }

    /**
     * Register the transaction.
     *
     * @param User $to
     * @param float $value The amount to be transfered
     * @return void
     * @throws InsufficientException
     * @throws InvalidArgumentException
     */
    public function transferTo(User $to, float $value)
    {
        if ($this->logist) {
            throw new InvalidTransferException();
        }

        if ($value < 0) {
            throw new InvalidArgumentException('Invalid given value.');
        }

        if ($this->balance < $value) {
            throw new InsufficientException();
        }

        $this->registerTransferInGatewayPayment();

        $this->sendTransferNotification();
        
        DB::beginTransaction();
        try {
            $this->addTransaction($to, $value);
    
            $this->balance -= $value;
            $this->save();
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Register a user's transaction.
     *
     * @param User $to
     * @param float $value
     * @return void
     */
    private function addTransaction(User $to, float $value)
    {
        $transaction = new Transaction();
        $transaction->value = $value;
        $transaction->user_id = $this->id;
        $transaction->to_user_id = $to->id;
        $transaction->save();
    }

    /**
     * Register transfer in payment gateway.
     *
     * @return void
     * @throws PaymentServiceInavailableException
     */
    private function registerTransferInGatewayPayment()
    {
        $url = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        $response = Http::get($url);

        if ($response['message'] !== 'Autorizado') {
            throw new PaymentServiceInavailableException();
        }
    }

    /**
     * Sends transfer notification.
     *
     * @return void
     * @throws PaymentServiceInavailableException
     */
    private function sendTransferNotification()
    {
        $url = 'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';
        $response = Http::get($url);

        if ($response['message'] !== 'Enviado') {
            throw new RuntimeException('There was a problem sending the notification.');
        }
    }
}
