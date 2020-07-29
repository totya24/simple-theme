<?php

namespace Teraone\Twig\Extension;

use DateTime;
use Twig_Environment;
use Twig_SimpleFilter;

/**
 * Cloudinary twig extension.
 *
 * @author Stefan Gotre <gotre@teraone.de>
 */
class StrftimeExtension extends \Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'strftime';
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('strftime', [$this, 'strftime'],
                array('needs_environment' => true)),
        );
    }

    /**
     * @param Twig_Environment $env
     * @param DateTime|string $date
     * @param string $format
     * @param mixed $timezone
     * @return string
     */
    public function strftime(Twig_Environment $env, $date,
                             $format = "%B %e, %Y %H:%M", $timezone = null)
    {
        $date = twig_date_converter($env, $date, $timezone);
        return strftime($format, $date->getTimestamp());
    }
}
