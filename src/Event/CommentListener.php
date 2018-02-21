<?php

namespace Spqr\Notifyoncomment\Event;

use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\Routing\Generator\UrlGenerator;
use Pagekit\Application\Exception;
use Pagekit\Mail\Message;
use Pagekit\Blog\Model\Comment;

/**
 * Class CommentListener
 *
 * @package Spqr\Notifyoncomment\Event
 */
class CommentListener implements EventSubscriberInterface
{
    
    /**
     * @param                             $event
     * @param \Pagekit\Blog\Model\Comment $comment
     * @param                             $request
     *
     * @return bool
     */
    public function onCommentCreated($event, Comment $comment, $request)
    {
        $config = App::module('spqr/notifyoncomment')->config();
        
        if (empty($config['users'])) {
            return false;
        } else {
            $content = $comment->content;
            $ip      = $comment->ip;
            $author  = $comment->author;
            
            $url      = App::url('@blog/comment', [],
                UrlGenerator::ABSOLUTE_URL);
            $sitename = App::config('system/site')->get('title');
            $title    = __('A new comment has been created on %sitename%.',
                ['%sitename%' => $sitename]);
            
            foreach ($config['users'] as $user) {
                try {
                    if (!empty($user['email'])) {
                        $mail = App::mailer()->create();
                        $mail->setTo($user['email'])->setSubject($title)
                            ->setBody(App::view('spqr/notifyoncomment:/views/mails/template.php',
                                compact('title', 'author', 'ip', 'content',
                                    'url')), 'text/html')->send();
                    }
                    
                } catch (\Exception $e) {
                    throw new Exception(__('Unable to send mail.'));
                }
            }
            
            return true;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'model.comment.created' => 'onCommentCreated',
        ];
    }
    
}