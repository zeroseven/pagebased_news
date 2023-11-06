 <?php

defined('TYPO3') || die('🧨');

call_user_func(static function () {
    $object = \Zeroseven\Pagebased\Registration\ObjectRegistration::create('Article')
        ->setClassName(\Zeroseven\PagebasedNews\Domain\Model\Article::class)
        ->setControllerClass(\Zeroseven\PagebasedNews\Controller\ArticleController::class)
        ->setRepositoryClass(\Zeroseven\PagebasedNews\Domain\Repository\ArticleRepository::class)
        ->enableDate(true)
//      ->enableTopics(1)
//      ->enableContact(1)
        ->enableRelations()
        ->enableTop()
        ->enableTags();

    $category = \Zeroseven\Pagebased\Registration\CategoryRegistration::create('Article-Category')
        ->setClassName(\Zeroseven\PagebasedNews\Domain\Model\Category::class)
        ->setRepositoryClass(\Zeroseven\PagebasedNews\Domain\Repository\CategoryRepository::class)
        ->setSorting('title')
        ->setDocumentType(148);

    $listPlugin = \Zeroseven\Pagebased\Registration\ListPluginRegistration::create('Article list')
        ->setDescription('Display objects of type "article" in a list');

    $filterPlugin = \Zeroseven\Pagebased\Registration\FilterPluginRegistration::create('Article filter')
        ->setDescription('Filter "article" objects');

    \Zeroseven\Pagebased\Registration\Registration::create('pagebased_news')
        ->setObject($object)
        ->setCategory($category)
        ->enableListPlugin($listPlugin)
        ->enableFilterPlugin($filterPlugin)
        ->store();
});
