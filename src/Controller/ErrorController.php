<?php

namespace Croogo\Core\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\Routing\Router;

/**
 * Error Handling Controller
 *
 * Controller used by ErrorHandler to render error views.  This is based
 * on CakePHP's own CakeErrorController with the following differences:
 * - loads its own set of components and helpers
 * - aware of Site.theme and Site.admin_theme
 *
 * @category Controllers
 * @package  Croogo.Croogo.Controller
 * @version  1.0
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ErrorController extends \Cake\Controller\ErrorController implements HookableComponentInterface
{
    use HookableComponentTrait;

    /**
     * Constructor
     *
     * @access public
     * @param Request $request
     * @param Response $response
     * @param null $name
     */
    public function __construct(Request $request = null, Response $response = null, $name = null)
    {
        parent::__construct($request, $response, $name);
        $this->eventManager()
            ->dispatch(new Event('Controller.afterConstruct', $this));
    }

    public function initialize()
    {
        $this->_dispatchBeforeInitialize();

        if (count(Router::extensions()) && !isset($this->RequestHandler)) {
            $this->loadComponent('RequestHandler');
        }

        $eventManager = $this->eventManager();
        if (isset($this->Auth)) {
            $eventManager->off($this->Auth);
        }
        if (isset($this->Security)) {
            $eventManager->off($this->Security);
        }

        parent::initialize();
    }

    /**
     * beforeFilter
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->viewBuilder()->className('Croogo/Core.Croogo');
        if ($this->request->param('prefix') === 'admin') {
            $adminTheme = Configure::read('Site.admin_theme');
            if ($adminTheme) {
                $this->viewBuilder()->theme($adminTheme);
            }
            $this->viewBuilder()->layout('admin_full');
        } elseif (Configure::read('Site.theme')) {
            $this->viewBuilder()
               ->theme(Configure::read('Site.theme'));
        }
    }
}
