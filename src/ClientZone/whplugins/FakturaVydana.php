<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClientZone\whplugins;

/**
 * Description of FakturaVydana
 *
 * @author vitex
 */
class FakturaVydana extends \ClientZone\WebHookHandler
{

    /**
     * Is invoice Settled
     * @return type
     */
    public function isSettled()
    {
        $changes = $this->getChanges();
        return isset($changes['datUhr']) && !empty($changes['datUhr']) ? true : false;
    }

    /**
     * Load Order Plugins
     *
     * @return int Loaded plugins count
     */
    public function productsToPlugins()
    {
        return count($this->plugins[$pluginName]);
    }

    public function getProdcodes()
    {
        $prodCodes  = [];
        $orderItems = $this->getDataValue('polozkyFaktury');
        if (count($orderItems)) {
            foreach ($orderItems as $orderItem) {
                if (!empty($orderItem['kod'])) {
                    $prodCodes[] = $orderItem['kod'];
                }
            }
        }
        return $prodCodes;
    }

    /**
     * Obtain instanced modules for invoice items
     * @return \ClientZone\OrderPlugin
     */
    public function orderModulesForInvoiceItems()
    {
        $orderModules = null;
        $prodCodes    = $this->getProdcodes();
        if (count($prodCodes)) {
            $d     = dir("ClientZone/orderplugins");
            while (false !== ($entry = $d->read())) {
                if ($entry[0] == '.') {
                    continue;
                }
                $pluginName = str_replace('.php', '', $entry);
                $className  = '\\ClientZone\\ClientZone\\orderplugins\\'.$pluginName;

                /**
                 * @var \ClientZone\OrderPlugin OrderPlugin
                 */
                $orderModules[$pluginName] = new $className;
                if (array_key_exists($orderModules[$pluginName]->productCode,
                        $prodCodes)) {
                    $orderModules[$pluginName]->easeShared = $this->easeShared;
                } else {
                    unset($orderModules[$pluginName]);
                }
            }
            $d->close();
        }
        return $orderModules;
    }

    /**
     * Invoice was updated. What to do now ?
     * 
     * @return boolean Change was processed. Ok remeber it
     */
    public function update()
    {
        if ($this->isSettled()) {
            $this->addStatusMessage(sprintf('Invoice %s was settled',
                    $this->getDataValue('kod')));

            $orderModules = $this->orderModulesForInvoiceItems();
            if (count($orderModules)) {
                foreach ($orderModules as $orderModule) {
                    $orderModule->settled();
                }
            }
        }
        return true;
    }
}
