<?php

namespace ProposalCrafter\WpMVC\Container\Exception;

\defined('ABSPATH') || exit;
use Exception;
use ProposalCrafter\Psr\Container\ContainerExceptionInterface;
class ContainerException extends Exception implements ContainerExceptionInterface
{
}
