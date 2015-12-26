<?php

namespace Vispanlab\AdminBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class VirtualExerciseController extends CRUDController {
    public function importXlsAction() {
        if ('POST' == $this->get('request')->getMethod()) {
            $objPHPExcel = @$this->container->get('phpexcel')->createPHPExcelObject($_FILES['import']['tmp_name']);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $this->container->get('vispanlab.virtualexercises.importer')->importVirtualExercise($objWorksheet, $this->admin->getClass());
            $this->container->get('session')->getFlashBag()->add('sonata_flash_success', 'Virtual exercises imported');
            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        } else {
            echo '<html><body><form enctype="multipart/form-data" action="#" method="POST"><input type="file" name="import" /><input type="submit" /></form></body></html>';die();
        }
    }
}
