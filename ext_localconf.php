 <?php

defined('TYPO3') || die('📰');

call_user_func(static function (string $_EXTKEY) {
    $object = \Zeroseven\Pagebased\Registration\ObjectRegistration::create('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:registration.object.title')
        ->setClassName(\Zeroseven\PagebasedNews\Domain\Model\Article::class)
        ->setControllerClass(\Zeroseven\PagebasedNews\Controller\ArticleController::class)
        ->setRepositoryClass(\Zeroseven\PagebasedNews\Domain\Repository\ArticleRepository::class)
        ->enableDate(true)
        ->enableRelations()
        ->enableTop()
        ->enableTags();

    $category = \Zeroseven\Pagebased\Registration\CategoryRegistration::create('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:registration.category.title')
        ->setClassName(\Zeroseven\PagebasedNews\Domain\Model\Category::class)
        ->setRepositoryClass(\Zeroseven\PagebasedNews\Domain\Repository\CategoryRepository::class)
        ->setDocumentType(148);

    $listPlugin = \Zeroseven\Pagebased\Registration\ListPluginRegistration::create('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:registration.list.title')
        ->setDescription('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:registration.list.description');

    $filterPlugin = \Zeroseven\Pagebased\Registration\FilterPluginRegistration::create('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:registration.filter.title')
        ->setDescription('LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:registration.filter.description');

    \Zeroseven\Pagebased\Registration\Registration::create($_EXTKEY)
        ->setObject($object)
        ->setCategory($category)
        ->enableListPlugin($listPlugin)
        ->enableFilterPlugin($filterPlugin)
        ->store();
}, 'pagebased_news');
