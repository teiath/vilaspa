<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vispanlab\AdminBundle\Admin\Comment;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CommentAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'createdAt' // name of the ordered field (default = the model id
    );
    protected $baseRoutePattern = 'comments';

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('createdAt', 'sonata_type_datetime_picker')
            ->add('body')
            ->add('author')
            //->add('state', 'sonata_comment_status', array('translation_domain' => 'SonataCommentBundle'))
            //->add('private', 'checkbox', array('required' => false))
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('body')
            ->add('author.username')
            ->add('author.email')
            //->add('state')
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('author')
            ->add('body', 'text')
            ->add('createdAt', 'datetime')
            ->add('thread.permalink', 'url')
            //->add('state', 'string', array('template' => 'SonataCommentBundle:CommentAdmin:list_status.html.twig'))
            //->add('private', 'boolean', array('editable' => true))
        ;
    }
}