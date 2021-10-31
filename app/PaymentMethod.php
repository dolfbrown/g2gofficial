<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    const TYPE_PAYPAL       = 1;
    const TYPE_CREDIT_CARD  = 2;
    const TYPE_MANUAL       = 3;
    const TYPE_OTHERS       = 4;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_methods';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                    'name',
                    'company_name',
                    'type',
                    'website',
                    'help_doc_url',
                    'admin_help_doc_link',
                    'terms_conditions_link',
                    'description',
                    'instructions',
                    'admin_description',
                    'enabled',
                    'order',
                ];

    /**
     * Scope a query to only include active records.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('enabled', 1);
    }

    /**
     * Return payment method type with details
     *
     * @return array
     */
    public function type()
    {
        return get_payment_method_type($this->type);
    }

    /**
     * Scope a query to only include active shops.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 1);
    }

    /**
     * Check if the payment method is configured
     */
    public function isConfigured()
    {
        switch ($this->code) {
            case 'paypal-express':
                return config('paypal_payment.account.client_id') && config('paypal_payment.account.client_secret');

            case 'stripe':
                return config('services.stripe.key') && config('services.stripe.secret');

            case 'authorize-net':
                return config('services.authorizenet.id') && config('services.authorizenet.key');

            case 'cybersource':
                return config('services.cybersource.merchant') && config('services.cybersource.key') && config('services.cybersource.secret');

            case 'instamojo':
                return config('services.instamojo.key') && config('services.instamojo.token');

            case 'paystack':
                return config('services.paystack.secret') ? TRUE : FALSE;

            case 'cod':
                return config('system_settings.cod_additional_details') && config('system_settings.cod_payment_instructions');

            case 'wire':
                return config('system_settings.wire_additional_details') && config('system_settings.wire_payment_instructions');

            default:
                return FALSE;
        }
    }

    /**
     * Payment method type string
     *
     * @param  int $type
     *
     * @return str
     */
    public function typeName($type)
    {
        switch ($type) {
            case \App\PaymentMethod::TYPE_PAYPAL:
                return trans('app.payment_method_type.paypal.name');

            case \App\PaymentMethod::TYPE_CREDIT_CARD:
                return trans('app.payment_method_type.credit_card.name');

            case \App\PaymentMethod::TYPE_MANUAL:
                return trans('app.payment_method_type.manual.name');

            default:
                return '';
        }
    }
}