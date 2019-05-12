<?php

namespace AppBundle\Controller;

use AppBundle\Content\Extractor;
use AppBundle\Document\Feed;
use AppBundle\Document\FeedItem;
use AppBundle\Repository\FeedItemRepository;
use AppBundle\Xml\SimplePieProxy;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * FeedItem controller.
 */
class FeedItemController extends Controller
{
    /**
     * Lists all Items documents related to a Feed.
     *
     * @param Feed $feed The document Feed (retrieving for a ParamConverter with the slug)
     *
     * @return Response
     */
    public function indexAction(Feed $feed, FeedItemRepository $feedItemRepository)
    {
        $feeditems = $feedItemRepository->findByFeed(
            $feed->getId(),
            $feed->getSortBy()
        );

        $deleteAllForm = $this->createDeleteAllForm();

        return $this->render('AppBundle:FeedItem:index.html.twig', [
            'menu' => 'feed',
            'feed' => $feed,
            'feeditems' => $feeditems,
            'delete_all_form' => $deleteAllForm->createView(),
        ]);
    }

    /**
     * Delete all items for a given Feed.
     *
     * @param Request $request
     * @param Feed    $feed    The document Feed (retrieving for a ParamConverter with the slug)
     *
     * @return RedirectResponse
     */
    public function deleteAllAction(Request $request, Feed $feed, FeedItemRepository $feedItemRepository, DocumentManager $dm, Session $session)
    {
        $form = $this->createDeleteAllForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $res = $feedItemRepository->deleteAllByFeedId($feed->getId());

            $feed->setNbItems(0);
            $dm->persist($feed);
            $dm->flush();

            $session->getFlashBag()->add('notice', $res['n'] . ' documents deleted!');
        }

        return $this->redirect($this->generateUrl('feed_edit', ['slug' => $feed->getSlug()]));
    }

    /**
     * Preview an item that is already cached.
     *
     * @param FeedItem $feedItem The document FeedItem (retrieving for a ParamConverter with the id)
     *
     * @return Response
     */
    public function previewCachedAction(FeedItem $feedItem)
    {
        return $this->render('AppBundle:FeedItem:content.html.twig', [
            'title' => $feedItem->getTitle(),
            'content' => $feedItem->getContent(),
            'url' => $feedItem->getLink(),
            'modal' => true,
        ]);
    }

    /**
     * Display a modal to preview the first item from a Feed.
     * It will allow to preview the parsed item (which isn't cached) using the internal or the external parser.
     *
     * @param Feed $feed The document Feed (retrieving for a ParamConverter with the slug)
     *
     * @return Response
     */
    public function testItemAction(Feed $feed)
    {
        return $this->render('AppBundle:FeedItem:preview.html.twig', [
            'feed' => $feed,
        ]);
    }

    /**
     * Following the previous action, this one will actually parse the content (for both parser).
     *
     * @param Request $request
     * @param Feed    $feed    The document Feed (retrieving for a ParamConverter with the slug)
     *
     * @return Response
     */
    public function previewNewAction(Request $request, Feed $feed, SimplePieProxy $simplePieProxy, Extractor $contentExtractor)
    {
        $rssFeed = $simplePieProxy
            ->setUrl($feed->getLink())
            ->init();

        try {
            $parser = $contentExtractor->init($request->get('parser'), $feed);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        $firstItem = $rssFeed->get_item(0);
        if (!$firstItem) {
            throw $this->createNotFoundException('No item found in this feed.');
        }

        $content = $parser->parseContent(
            $firstItem->get_permalink(),
            $firstItem->get_description()
        );

        return $this->render('AppBundle:FeedItem:content.html.twig', [
            'title' => html_entity_decode($firstItem->get_title(), ENT_COMPAT, 'UTF-8'),
            'content' => $content->content,
            'modal' => false,
            'url' => $content->url,
            'defaultContent' => $content->useDefault,
        ]);
    }

    private function createDeleteAllForm()
    {
        return $this->createFormBuilder()->getForm();
    }
}
