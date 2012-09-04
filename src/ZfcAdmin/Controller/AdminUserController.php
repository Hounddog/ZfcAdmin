<?php

namespace ZfcAdmin\Controller;

use ZfcUser\Controller\UserController;

class AdminUserController extends UserController
{
    /**
     * get options
     *
     * @return UserControllerOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof UserControllerOptionsInterface) {
            $this->setOptions($this->getServiceLocator()->get('zfcadmin_module_options'));
        }
        return $this->options;
    }

    /**
     * Login form
     */
    public function loginAction()
    {
        $this->getServiceLocator()->get('zfcuser_user_mapper');
        $request = $this->getRequest();
        $form    = $this->getLoginForm();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        if (!$request->isPost()) {
            return array(
                'loginForm' => $form,
                'redirect'  => $redirect,
            );
        }

        $form->setData($request->getPost());

        if (!$form->isValid()) {
            $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
            return $this->redirect()->toUrl($this->url('admin')->fromRoute('admin/login').($redirect ? '?redirect='.$redirect : ''));
        }
        // clear adapters

        return $this->forward()->dispatch('zfcadminuser', array('action' => 'authenticate'));
    }
}