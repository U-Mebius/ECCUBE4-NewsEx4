<?php

/*
 * This file is part of NewsEx4
 *
 * Copyright(c) U-Mebius Inc. All Rights Reserved.
 *
 * https://umebius.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\NewsEx4\Controller;

use Eccube\Controller\AbstractController;
use Eccube\Entity\News;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\NewsType;
use Eccube\Repository\NewsRepository;
use Eccube\Util\CacheUtil;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * NewsController constructor.
     *
     * @param NewsRepository $newsRepository
     */
    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * 新着情報一覧を表示する。
     *
     * @Route("/news", name="plg_news")
     * @Template("NewsEx4/Resource/template/index.twig")
     *
     * @param Request $request
     * @param int $page_no
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $page_no = $request->query->get('pageno', 1);
        $qb = $this->newsRepository->getQueryBuilderAll();

        $qb->andWhere('n.visible = 1');

        $pagination = $paginator->paginate(
            $qb,
            $page_no,
            10
        );

        return [
            'pagination' => $pagination,
        ];
    }


    /**
     * 新着情報一覧を表示する。
     *
     * @Route("/news/{id}", name="plg_news_detail")
     * @Template("NewsEx4/Resource/template/detail.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function detail(Request $request, News $News)
    {
        if ($News->isVisible() == 0) {
            throw new NotFoundHttpException();
        }

        return [
            'News' => $News,
        ];
    }


}
