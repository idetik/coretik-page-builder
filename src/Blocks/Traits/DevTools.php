<?php

namespace Coretik\PageBuilder\Blocks\Traits;

use Coretik\PageBuilder\Blocks\Modifier\DevToolsTabModifier;

trait DevTools
{
    public function initializeDevTools()
    {
        if ('development' === WP_ENV) {
            \add_filter('coretik/page-builder/block/template_placehoder', [$this, 'getDevToolsView']);
            DevToolsTabModifier::modify($this);
        }
    }

    public function getDevToolsView(): string
    {
        \ob_start();
        \do_action('coretik/page-builder/block/devtools/start_view', $this);
        ?>
        <div>
            <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap">
                <div><b>Block:</b><pre style="margin-top:4px"><?= $this->getName() ?></pre></div>
                <div><b>UniqId:</b> <pre style="margin-top:4px"><?= $this->getUniqId() ?></pre></div>
                <div><b>Template:</b> <pre style="margin-top:4px"><?= $this->template() ?></pre></div>
            </div>
            <div style="margin-top:16px;max-height:300px;overflow:auto;"><b>Data:</b> <pre style="padding-top:0;padding-bottom:0;margin-top:4px;"><?php \highlight_string("<?php\n" . var_export($this->getParameters(), true) . ";\n?>"); ?></pre></div>
            <div style="margin-top:16px;max-height:300px;overflow:auto;"><b>Rendu:</b> <pre style="margin-top:4px;"><code><?= htmlspecialchars($this->render(true)); ?></code></pre></div>
        </div>
        <?php
        \do_action('coretik/page-builder/block/devtools/end_view', $this);

        return \apply_filters('coretik/page-builder/block/devtools/view', \ob_get_clean(), $this);
    }
}
