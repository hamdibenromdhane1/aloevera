<?php

use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Sandbox\SecurityError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedTagError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFilterError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFunctionError;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;

/* newsletter/templates/blocks/header/block.hbs */
class __TwigTemplate_9bdadcd8aa2f8ff8e940d5355ea2d65ca734ada45b87591ab4e86ef3b3e778e1 extends \MailPoetVendor\Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 1
        echo "<div class=\"mailpoet_tools\"></div>
<style type=\"text/css\">
    .mailpoet_editor_view_{{ viewCid }} .mailpoet_content,
    .mailpoet_editor_view_{{ viewCid }} .mailpoet_content p {
        color: {{ model.styles.text.fontColor }};
        font-family: {{fontWithFallback model.styles.text.fontFamily }};
        font-size: {{ model.styles.text.fontSize }};
        background-color: {{ model.styles.block.backgroundColor }};
        text-align: {{ model.styles.text.textAlign }};
    }
    .mailpoet_editor_view_{{ viewCid }} .mailpoet_content a,
    .mailpoet_editor_view_{{ viewCid }} .mailpoet_content a:hover,
    .mailpoet_editor_view_{{ viewCid }} .mailpoet_content a:active,
    .mailpoet_editor_view_{{ viewCid }} .mailpoet_content a:visited {
        color: {{ model.styles.link.fontColor }};
        text-decoration: {{ model.styles.link.textDecoration }};
    }
</style>
<div class=\"mailpoet_content mailpoet_text_content\" data-automation-id=\"header\">{{{ model.text }}}</div>
<div class=\"mailpoet_block_highlight\"></div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/header/block.hbs";
    }

    public function getDebugInfo()
    {
        return array (  30 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/header/block.hbs", "C:\\xampp\\htdocs\\wp-content\\plugins\\mailpoet\\views\\newsletter\\templates\\blocks\\header\\block.hbs");
    }
}
