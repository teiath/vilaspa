<?php

namespace Vispanlab\CommonBundle\Extension;

use FOS\CommentBundle\Twig\CommentExtension;

class FixedCommentExtension extends CommentExtension
{
    public function getTests()
    {
        return array(
            'fos_comment_deleted'         => new \Twig_SimpleTest('fos_comment_deleted', array($this, 'isCommentDeleted')),
            'fos_comment_in_state'        => new \Twig_SimpleTest('fos_comment_in_state', array($this, 'isCommentInState')),
            'fos_comment_votable'         => new \Twig_SimpleTest('fos_comment_votable', array($this, 'isVotable')),
            'fos_comment_raw'             => new \Twig_SimpleTest('fos_comment_raw', array($this, 'isRawComment')),
        );
    }
}
