<?php

namespace Claroline\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Claroline\UserBundle\Entity\User;

/**
 * Note : Actions of this controller are not routed. They're intended to be
 *        rendered directly in the "core_layout" and "app_layout" templates.
 *        Note that defining this controller as a service is incompatible
 *        with this technique (the twig/symfony "render" statement seems to 
 *        work only with the symfony Controller class).
 */
class LayoutController extends Controller
{
    public function headerAction()
    {
        return $this->render('ClarolineCommonBundle:Layout:header.html.twig');
    }

    public function footerAction()
    {
        return $this->render('ClarolineCommonBundle:Layout:footer.html.twig');
    }

    public function statusBarAction()
    {
        $connected = false;
        $username = null;
        $loginTarget = null;
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($user instanceof User)
        {
            $connected = true;
            $username = $user->getFirstName() . ' ' . $user->getLastName();
        }
        else
        {
            $applicationEntity = 'Claroline\PluginBundle\Entity\Application';
            $appRepo = $this->getDoctrine()
                ->getEntityManager()
                ->getRepository($applicationEntity);
            $targetApp = $appRepo->getConnectionTargetApplication();            
            $loginTarget = $targetApp ? $targetApp->getIndexRoute() : 'claro_security_login';
        }

        return $this->render(
            'ClarolineCommonBundle:Layout:status_bar.html.twig', 
            array(
                'connected' => $connected,
                'username' => $username,
                'login_target' => $loginTarget
            )
        );
    }

    public function applicationMenuAction()
    {
        $launcherEntity = 'Claroline\PluginBundle\Entity\ApplicationLauncher';
        $launcherRepo = $this->getDoctrine()
            ->getEntityManager()
            ->getRepository($launcherEntity);
        $user = $this->container->get('security.context')->getToken()->getUser();
        $user instanceof User ? $roles = $user->getRoles() : $roles = array('ROLE_ANONYMOUS');
        $launchers = $launcherRepo->findByAccessRoles($roles);

        return $this->render(
            'ClarolineCommonBundle:Layout:app_menu.html.twig', 
            array('launchers' => $launchers)
        );
    }
}