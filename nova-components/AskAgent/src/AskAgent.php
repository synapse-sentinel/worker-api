<?php

namespace SynapseSentinel\AskAgent;

use Laravel\Nova\ResourceTool;

class AskAgent extends ResourceTool
{
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Ask Agent';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'ask-agent';
    }
}
