<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\ExpressionLanguage;

use Symfony\Component\DependencyInjection\ExpressionLanguage as BaseExpressionLanguage;

class ExpressionLanguage extends BaseExpressionLanguage
{
    /**
     * {@inheritdoc}
     */
    public function __construct($cache = null, array $providers = array(), callable $serviceCompiler = null)
    {
        // prepend the default provider to let users override it easily
        array_unshift($providers, new PimcoreLanguageProvider($serviceCompiler));
        array_unshift($providers, new PHPFunctionsProvider($serviceCompiler));

        parent::__construct($cache, $providers);
    }
}
