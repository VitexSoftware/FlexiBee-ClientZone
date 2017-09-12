<?php
/**
 * shop4flexibee - Objednavka.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee;

/**
 * Description of Order
 *
 * @author vitex
 */
class OrderItem extends FlexiBeeEngine
{
    /**
     * Customer
     * @var Customer
     */
    public $customer;

    /**
     * Pricelist
     * @var \FlexiPeeHP\Cenik
     */
    public $cenik = null;

    /**
     * Currency Amount
     * @var float
     */
    public $credit = null;

    /**
     * Service FlexiBee Code
     * @var string
     */
    public $service = null;

    /**
     * Domain Name or Phone Number
     * @var string
     */
    public $detail = null;

    /**
     * Array of Form Plugins
     * @var array
     */
    public $plugins = [];

    /**
     * Order Class
     * @param array $formdata
     */
    public function __construct($formdata = array())
    {
        parent::__construct();
        $this->takeData($formdata);
        $this->customer = new Customer();
        $this->cenik    = new \FlexiPeeHP\Cenik();
        $this->loadPlugins();
    }

    /**
     * Data to use in object
     *
     * @param array $data
     * @return int
     */
    public function takeData($data)
    {
        if (isset($data['lmsid'])) {
            $data['lmsid'] = intval($data['lmsid']);
        }
        return parent::takeData($data);
    }

    /**
     * Load Order Plugins
     *
     * @return int Loaded plugins count
     */
    public function loadPlugins()
    {
        $d     = dir("Shop4FlexiBee/orderplugins");
        while (false !== ($entry = $d->read())) {
            if ($entry[0] == '.') {
                continue;
            }
            $pluginName = str_replace('.php', '', $entry);
            $className  = '\\Shop4FlexiBee\\orderplugins\\'.$pluginName;

            /**
             * @var \Shop4FlexiBee\OrderPlugin OrderPlugin
             */
            $this->plugins[$pluginName]             = new $className;
            $this->plugins[$pluginName]->easeShared = $this->easeShared;
        }
        $d->close();
        return count($this->plugins[$pluginName]);
    }

    /**
     * Check order input
     *
     * @return boolean
     */
    public function checkInput()
    {
        return $this->checkService($this->getDataValue('service')) && $this->checkByPlugin();
    }

    /**
     * Check if service/product is choosen
     * 
     * @param string $service
     * @return boolean
     */
    public function checkService($service)
    {
        $ok = false;
        if (!strlen($service)) {
            $this->addStatusMessage(_('Please choose service'), 'info');
        } else {
            if ($service == 'Common') {
                $code = $this->getDataValue('kod');
            } else {
                $code = urlencode($this->plugins[$service]->productCode);
            }

            if (strlen(trim($code))) {
                $this->cenik->loadFromFlexiBee('code:'.$code);
                if ($this->cenik->lastResponseCode == 200) {
//                $this->addStatusMessage($this->cenik->getDataValue('nazev'),
//                    'success');
                    $ok = true;
                } else {
                    $this->addStatusMessage(sprintf(_('Product with code %s does not exist'),
                            $code), 'warning');
                    $ok = false;
                }
            } else {
                $this->addStatusMessage(sprintf(_('Missing product code'), $code),
                    'warning');
                $ok = false;
            }
        }
        return $ok;
    }

    /**
     * Check requirements for form choosen Plugin\
     *
     * @return boolean
     */
    public function checkByPlugin()
    {
        return $this->getServicePlugin()->controlFields($this);
    }

    /**
     * Obtain Curent Service plugin object
     *
     * @return OrderPlugin
     */
    public function getServicePlugin()
    {
        $servicePlugin = null;
        $service       = $this->getDataValue('service');
        if (isset($this->plugins[$service]) && is_object($this->plugins[$service])) {
            $servicePlugin = $this->plugins[$service];
        }
        return $servicePlugin;
    }

    /**
     * Make proforma for customer
     *
     * @param float $credit
     * @param string $service
     *
     * @return \FlexiPeeHP\FakturaVydana
     */
    public function makeProforma()
    {
        $polozka = $this->getServicePlugin()->processFields($this);

        $proforma = new \FlexiPeeHP\FakturaVydana();

        $today = date('Y-m-d');
        $proforma->setDataValue('firma', $this->customer->adresar);
        $proforma->setDataValue('typDokl', 'code:ZALOHA');
        $proforma->setDataValue('uvodTxt',
            $this->cenik->getDataValue('nazev').' '.$polozka['poznam']);
        $proforma->setDataValue('zavTxt', '');
        $proforma->setDataValue('poznam', 'WebForm');
        $proforma->setDataValue('duzpPuv', $today);
        $proforma->setDataValue('duzpUcto', $today);
        $proforma->setDataValue('datUcto', $today);
        $proforma->setDataValue('stitky', ['SYSTEM', 'API']);
        $proforma->addArrayToBranch($polozka, 'polozkyFaktury');
        //$proforma->debug = true;
        $proforma->insertToFlexiBee();

        if (\FlexiPeeHP\Priloha::addAttachment($proforma, 'order.json',
                json_encode(array_merge($polozka,
                        ['service' => $this->getDataValue('service')])),
                'application/json') != 201) {
            $proforma->addStatusMessage('Saving Order Details '.$proforma->getFlexiBeeURL().' Failed',
                'error');
        }

        return $proforma;
    }

    public function addToCart()
    {
        $polozka               = $this->getServicePlugin()->processFields($this);
        $polozka['service']    = $this->getDataValue('service');
        $this->cenik->loadFromFlexiBee($polozka['cenik']);
        $polozka['typZasobyK'] = $this->cenik->getDataValue('typZasobyK');
        $polozka['icon']       = $this->cenik->getDataValue('id');
        $_SESSION['cart'][]    = $polozka;
        $this->addStatusMessage(sprintf(_('%s added to cart'),
                $this->cenik->getDataValue('nazev')), 'success');
    }

}
