<?php

namespace Paymentez\PaymentGateway\Model\Adminhtml\Source;

use Magento\Framework\Data\OptionSourceInterface;

class InstallmentsTypes implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value'=> 1, 'label'=> __('Revolving and deferred without interest (The bank will pay to the commerce the installment, month by month)(Ecuador)')],
            ['value'=> 2, 'label'=> __('Deferred with interest (Ecuador, México)')],
            ['value'=> 3, 'label'=> __('Deferred without interest (Ecuador, México)')],
            ['value'=> 7, 'label'=> __('Deferred with interest and months of grace (Ecuador)')],
            ['value'=> 6, 'label'=> __('Deferred without interest pay month by month (Ecuador)(Medianet)')],
            ['value'=> 9, 'label'=> __('Deferred without interest and months of grace (Ecuador, México)')],
            ['value'=> 10, 'label'=> __('Deferred without interest and months of grace (Ecuador)(Medianet)')],
            ['value'=> 21, 'label'=> __('Deferred without interest promotion bimonthly (Ecuador)')],
            ['value'=> 22, 'label'=> __('For Diners Club exclusive, deferred with and without interest (Ecuador)')],
            ['value'=> 30, 'label'=> __('Deferred with interest pay month by month (Ecuador)(Medianet)')],
            ['value'=> 50, 'label'=> __('Deferred without interest promotions (Supermaxi)(Ecuador)(Medianet)')],
            ['value'=> 51, 'label'=> __('Deferred with interest (Cuota fácil)(Ecuador)(Medianet)')],
            ['value'=> 52, 'label'=> __('Without interest (Rendecion Produmillas)(Ecuador)(Medianet)')],
            ['value'=> 53, 'label'=> __('Without interest sale with promotions (Ecuador)(Medianet)')],
            ['value'=> 70, 'label'=> __('Deferred special without interest (Ecuador)(Medianet)')],
            ['value'=> 72, 'label'=> __('Credit without interest (cte smax)(Ecuador)(Medianet)')],
            ['value'=> 73, 'label'=> __('Special credit without interest (smax)(Ecuador)(Medianet)')],
            ['value'=> 74, 'label'=> __('Prepay without interest (smax)(Ecuador)(Medianet)')],
            ['value'=> 75, 'label'=> __('Defered credit without interest (smax)(Ecuador)(Medianet)')],
            ['value'=> 90, 'label'=> __('Without interest with months of grace (Supermaxi)(Ecuador)(Medianet)')],
        ];
    }
}
