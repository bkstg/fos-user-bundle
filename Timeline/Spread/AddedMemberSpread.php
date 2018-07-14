<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\Timeline\Spread;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\EntryUnaware;
use Spy\Timeline\Spread\SpreadInterface;

class AddedMemberSpread implements SpreadInterface
{
    /**
     * You spread class is support the action ?
     *
     * @param ActionInterface $action
     *
     * @return bool
     */
    public function supports(ActionInterface $action)
    {
        if ('added_member' != $action->getVerb()) {
            return false;
        }

        return true;
    }

    /**
     * @param ActionInterface $action     action we look for spreads
     * @param EntryCollection $coll       Spreads defined on an EntryCollection
     * @param EntryCollection $collection
     *
     * @return void
     */
    public function process(ActionInterface $action, EntryCollection $collection): void
    {
        $member = $action->getComponent('directComplement')->getData();
        $group = $action->getComponent('indirectComplement')->getData();

        // Add an entry to the group and member timeline.
        $collection->add(new EntryUnaware(get_class($group), $group->getId()));
        $collection->add(new EntryUnaware(get_class($member), $member->getId()));
    }
}
