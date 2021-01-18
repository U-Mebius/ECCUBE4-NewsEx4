<?php

namespace Plugin\NewsEx4;

use Eccube\Entity\Layout;
use Eccube\Entity\Page;
use Eccube\Entity\PageLayout;
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Repository\LayoutRepository;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\PageRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PluginManager.
 */
class PluginManager extends AbstractPluginManager
{
    private $pages = [
//        Resource/template/default
        [
            'name' => 'ニュース一覧',
            'url' => 'plg_news',
            'file_name' => 'NewsEx4/Resource/template/index',
        ],
        [
            'name' => 'ニュース',
            'url' => 'plg_news_detail',
            'file_name' => 'NewsEx4/Resource/template/detail',
        ],
    ];

    public function enable(array $meta, ContainerInterface $container)
    {
        $this->createPageLayout($container);
    }

    public function disable(array $meta, ContainerInterface $container)
    {
        $this->removePageLayout($container);
    }

    public function update(array $meta, ContainerInterface $container)
    {
        $this->createPageLayout($container);
    }

    private function createPageLayout(ContainerInterface $container)
    {
        foreach ($this->pages as $row) {
            $PageLayout = $container->get(PageRepository::class)->findOneBy(['url' => $row['url']]);
            if (is_null($PageLayout)) {
                // pagelayoutの作成
                // ページレイアウトにプラグイン使用時の値を代入
                /** @var \Eccube\Entity\Page $Page */
                $Page = $container->get(PageRepository::class)->newPage();
                $Page->setEditType(Page::EDIT_TYPE_DEFAULT);
                $Page->setName($row['name']);
                $Page->setUrl($row['url']);
                $Page->setFileName($row['file_name']);
                $Page->setMetaRobots('');

                // DB登録
                $entityManager = $container->get('doctrine')->getManager();
                $entityManager->persist($Page);
                $entityManager->flush($Page);

                $Layout = $container->get(LayoutRepository::class)->find(Layout::DEFAULT_LAYOUT_UNDERLAYER_PAGE);
                $PageLayout = new PageLayout();
                $PageLayout->setPage($Page)
                    ->setPageId($Page->getId())
                    ->setLayout($Layout)
                    ->setLayoutId($Layout->getId())
                    ->setSortNo(0);

                $entityManager->persist($PageLayout);
                $entityManager->flush($PageLayout);
            }
        }
    }

    /**
     * クーポン用ページレイアウトを削除.
     */
    private function removePageLayout(ContainerInterface $container)
    {
        foreach ($this->pages as $row) {
            // ページ情報の削除
            $Page = $container->get(PageRepository::class)->findOneBy(['url' => $row['url']]);
            if ($Page) {
                $Layout = $container->get(LayoutRepository::class)->find(Layout::DEFAULT_LAYOUT_UNDERLAYER_PAGE);
                $PageLayout = $container->get(PageLayoutRepository::class)->findOneBy(['Page' => $Page, 'Layout' => $Layout]);
                // Blockの削除
                $entityManager = $container->get('doctrine')->getManager();
                $entityManager->remove($PageLayout);
                $entityManager->remove($Page);
                $entityManager->flush();
            }
        }
    }
}
