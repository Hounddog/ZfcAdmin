<?php

namespace ZfcAdmin\Options;

use ZfcUser\Options\ModuleOptions as UserModuleOptions;

class ModuleOptions extends UserModuleOptions
{
    /**
     * @var string
     */
    protected $loginRedirectRoute = 'admin';

    /**
     * @var string
     */
    protected $logoutRedirectRoute = 'admin/login';
}
