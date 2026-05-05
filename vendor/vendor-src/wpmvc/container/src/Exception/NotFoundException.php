<?php

namespace ProposalCrafter\WpMVC\Container\Exception;

\defined('ABSPATH') || exit;
use Exception;
use ProposalCrafter\Psr\Container\NotFoundExceptionInterface;
class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
