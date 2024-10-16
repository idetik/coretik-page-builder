<?php

namespace Coretik\PageBuilder\Core\Block\Traits;

use Coretik\PageBuilder\Core\Block\Modifier\DevToolsTabModifier;

trait DevTools
{
    public static function bootDevToolsOnce(): void
    {
        // // Prepare and hide dev fields on production envs
        \add_filter('acf/prepare_field/name=devtools', [__CLASS__, 'prepareDevToolsView']);
        \add_filter('acf/prepare_field/name=devtools_tab', [__CLASS__, 'prepareDevToolsTab']);
    }

    public function initializeDevTools()
    {
        // Apply modifier, add tab and dynamic render field
        DevToolsTabModifier::modify($this);
    }

    public function getDevToolsView(bool $rendering = false): string
    {
        \ob_start();
        \do_action('coretik/page-builder/block/devtools/start_view', $this);

        $template_exists = $this->templateExists();
        ?>
        <div>
            <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap">
                <div><b>Block:</b><pre style="margin-top:4px"><?= $this->getName() ?></pre></div>
                <div><b>UniqId:</b> <pre style="margin-top:4px"><?= $this->getUniqId() ?></pre></div>
                <div><b>Template:</b> <pre style="margin-top:4px"><span style="color:<?= $template_exists ? 'green' : 'red' ?>" class="dashicons dashicons-<?= $template_exists ? 'yes' : 'no' ?>" title="<?= $template_exists ? 'Template exists.' : 'Template not found.' ?>"></span><?= $this->template() ?></pre></div>
            </div>
            <div style="margin-top:16px;max-height:300px;overflow:auto;"><b>Data:</b> <pre style="padding-top:0;padding-bottom:0;margin-top:4px;"><?php
                $parameters = array_map(
                    fn ($row) => match (gettype($row)) {
                        'object' => $row::class,
                        default => $row
                    },
                    $this->getParameters()
                                                                                                                                                  );
                                                                                                                                                  \highlight_string("<?php\n" . var_export($parameters, true) . ";\n?>");
                                                                                                                                                    ?></pre></div>
            <div style="margin-top:16px;max-height:300px;overflow:auto;"><b>Rendu:</b> <pre style="margin-top:4px;"><code><?= htmlspecialchars($this->render(true)); ?></code></pre></div>
        </div>
        <?php
        \do_action('coretik/page-builder/block/devtools/end_view', $this);

        return \apply_filters('coretik/page-builder/block/devtools/view', \ob_get_clean(), $this);
    }

    public static function prepareDevToolsTab(array $field): array
    {
        if (!static::shouldDisplayDevTools()) {
            return [];
        }

        return $field;
    }

    public static function prepareDevToolsView(array $field): array
    {
        if (!static::shouldDisplayDevTools()) {
            return [];
        }

        return $field;
    }

    public static function shouldDisplayDevTools(): bool
    {
        return \apply_filters(
            'coretik/page-builder/block/devtools/enabled',
            (defined('WP_ENV') && 'development' === WP_ENV) || (!defined('WP_ENV') && \current_user_can('manage_options'))
        );
    }
}
