<?php

namespace Botble\Ecommerce\Forms;

use BaseHelper;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Http\Requests\CustomerCreateRequest;
use Botble\Ecommerce\Models\CorporateCode;
use Botble\Ecommerce\Models\Customer;

class CustomerForm extends FormAbstract
{

    /**
     * @var string
     */
    protected $template = 'core/base::forms.form-tabs';

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $code = CorporateCode::pluck('list_of_corporate_code','list_of_corporate_code')->prepend('Select Corporate Code','')->toArray();
        $this
            ->setupModel(new Customer)
            ->setValidatorClass(CustomerCreateRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('email', 'text', [
                'label'      => trans('plugins/ecommerce::customer.email'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('plugins/ecommerce::customer.email_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->add('subs_contractor', 'radio', [
                'label'      => 'Sub contractor',
                'label_attr' => ['class' => 'control-label'],
                'value'      => 1,
                'attr'       => [
                    'name'  => 'subs_contractor',
                    ($this->getModel()->ranking == 1 ? 'checked' : ' ')
                ],
            ])
            ->add('corporate', 'radio', [
                'label'      => 'Corporate',
                'label_attr' => ['class' => 'control-label'],
                'value'      => 2,
                'attr'       => [
                    'name'  => 'subs_contractor',
                    ($this->getModel()->ranking == 2 ? 'checked' : ' ')
                ],
            ])
            ->add('company', 'text', [
                'label'      => trans('plugins/ecommerce::customer.company'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control corporate_field',
                    'placeholder'  => trans('plugins/ecommerce::customer.company_placeholder'),
                    'data-counter' => 20,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->ranking == 2 ? null : 'hidden'),
                ],
            ])
            ->add('biz_number', 'text', [
                'label'      => trans('plugins/ecommerce::customer.biz_number'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control corporate_field',
                    'placeholder'  => trans('plugins/ecommerce::customer.biz_number_placeholder'),
                    'data-counter' => 20,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->ranking == 2 ? null : 'hidden'),
                ],
            ])
            ->add('company_doc_url', 'hidden', [
                'attr'       => [
                    'class'        => 'form-control corporate_field',
                    'data-counter' => 20,
                ],
                'value'        => $this->getModel()->company_doc,
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->ranking == 2 ? null : 'hidden'),
                ],
            ])->add('corporate_code', 'customSelect', [
                'label'      => trans('Corporate Code'),
                'label_attr' => ['class' => 'control-label required'],
                'choices'    => $code,
            ])
            ->add('company_doc', 'file', [
                'label'      => trans('plugins/ecommerce::customer.company_doc'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'        => 'form-control corporate_field',
                    'data-counter' => 20,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->ranking == 2 ? null : 'hidden'),
                ],
            ])
            ->add('representive_allowed', 'text', [
                'label'      => trans('plugins/ecommerce::customer.representive_allowed'),
                'label_attr' => ['class' => 'control-label corpo'],
                'attr'       => [
                    'class'        => 'form-control corporate_field corpo',
                    'placeholder'  => trans('plugins/ecommerce::customer.representive_allowed'),
                    'data-counter' => 20,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->ranking == 2 ? null : 'hidden'),
                ],
            ])
            ->add('site_approval_allowed', 'text', [
                'label'      => trans('plugins/ecommerce::customer.site_approval_allowed'),
                'label_attr' => ['class' => 'control-label corpo'],
                'attr'       => [
                    'class'        => 'form-control corporate_field corpo',
                    'placeholder'  => trans('plugins/ecommerce::customer.site_approval_allowed'),
                    'data-counter' => 20,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->ranking == 2 ? null : 'hidden'),
                ],
            ])
            ->add('phone', 'text', [
                'label'      => trans('plugins/ecommerce::customer.phone'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => trans('plugins/ecommerce::customer.phone_placeholder'),
                    'data-counter' => 20,
                ],
            ])
            ->add('dob', 'date', [
                'label'      => trans('plugins/ecommerce::customer.dob'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'data-date-format' => config('core.base.general.date_format.js.date'),
                ],
                'default_value' => BaseHelper::formatDate(now()),
            ])
            ->add('is_change_password', 'checkbox', [
                'label'      => trans('plugins/ecommerce::customer.change_password'),
                'label_attr' => ['class' => 'control-label'],
                'value'      => 1,
            ])
            ->add('password', 'password', [
                'label'      => trans('plugins/ecommerce::customer.password'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'data-counter' => 60,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->id ? ' hidden' : null),
                ],
            ])
            ->add('password_confirmation', 'password', [
                'label'      => trans('plugins/ecommerce::customer.password_confirmation'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'data-counter' => 60,
                ],
                'wrapper'    => [
                    'class' => $this->formHelper->getConfig('defaults.wrapper_class') . ($this->getModel()->id ? ' hidden' : null),
                ],
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices'    => CustomerStatusEnum::labels(),
            ])
            ->add('avatar', 'mediaImage', [
                'label'      => trans('core/base::forms.image'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->setBreakFieldPoint('status');
    }
}
