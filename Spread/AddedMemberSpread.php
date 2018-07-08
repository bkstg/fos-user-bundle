<?php

namespace Bkstg\FOSUserBundle\Spread;

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
     * @return boolean
     */
    public function supports(ActionInterface $action)
    {
        if ($action->getVerb() != 'added_member') {
            return false;
        }
        return true;
    }

    /**
     * @param  ActionInterface $action action we look for spreads
     * @param  EntryCollection $coll   Spreads defined on an EntryCollection
     * @return void
     */
    public function process(ActionInterface $action, EntryCollection $collection)
    {
        $member = $action->getComponent('directComplement')->getData();
        $group = $action->getComponent('indirectComplement')->getData();

        // Add an entry to the group and member timeline.
        $collection->add(new EntryUnaware(get_class($group), $group->getId()));
        $collection->add(new EntryUnaware(get_class($member), $member->getId()));
    }
}
