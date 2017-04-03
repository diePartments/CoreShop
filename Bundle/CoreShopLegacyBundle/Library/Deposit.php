<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @author Stefan Hagspiel <shagspiel@dachcom.ch>
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Library;

/**
 * Class Deposit
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Library
 */
class Deposit
{
    /**
     * Deposit Namespace.
     *
     * @var string
     */
    public $depositNamespace = null;

    /**
     * Deposit Data.
     *
     * @var array
     */
    public $depositData = [];

    /**
     * set max elements per deposit
     * set 0 for no limit.
     *
     * @var int
     */
    public $maxElements = 0;

    /**
     * Limit Reached identifier.
     */
    const LIMIT_REACHED = 'limit_reached';

    /**
     * Already added identifer.
     */
    const ALREADY_ADDED = 'already_added';

    /**
     * Set Deposit Namespace.
     *
     * @param string $namespace
     *
     * @return $this
     */
    protected function setNamespace($namespace = null)
    {
        $session = $this->getSession();

        $this->depositNamespace = $namespace;

        if (!isset($session->deposits)) {
            $session->deposits = [];
        }

        if (!isset($session->deposits[ $this->depositNamespace ])) {
            $session->deposits[ $this->depositNamespace ] = [];
        }

        $this->depositData = $session->deposits[ $this->depositNamespace ];

        return $this;
    }

    /**
     * set Limit.
     *
     * @param int $limit
     *
     * @return $this
     */
    protected function setLimit($limit = 0)
    {
        $this->maxElements = $limit;

        return $this;
    }

    /**
     * Get formatted deposit.
     *
     * @return array
     */
    protected function toArray()
    {
        if (empty($this->depositData)) {
            return [];
        }

        $data = [];

        foreach ($this->depositData as $id => $val) {
            $data[] = $id;
        }

        return $data;
    }

    /**
     * Add a Element to Deposit.
     *
     * @param      $id
     * @param bool $value
     *
     * @return bool
     */
    public function add($id, $value = true)
    {
        if ($this->allowedToAdd($id) === true) {
            $this->depositData[ (int) $id ] = $value;
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Remove a Element from Deposit.
     *
     * @param $id
     */
    public function remove($id)
    {
        if (isset($this->depositData[ $id ])) {
            unset($this->depositData[ $id ]);
        }

        $this->save();
    }

    /**
     * Check if element is allowed to add.
     *
     * @param $id
     *
     * @return string|boolean
     */
    public function allowedToAdd($id)
    {
        if ($this->maxElements !== 0 && count($this->depositData) >= $this->maxElements) {
            return self::LIMIT_REACHED;
        } elseif (isset($this->depositData[ $id ])) {
            return self::ALREADY_ADDED;
        }

        return true;
    }

    /**
     * get Session.
     *
     * @return \Pimcore\Tool\Session
     */
    protected function getSession()
    {
        return \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getTools()->getSession();
    }

    /**
     * Save to session.
     */
    protected function save()
    {
        $session = $this->getSession();

        $session->deposits[ $this->depositNamespace ] = $this->depositData;
    }
}