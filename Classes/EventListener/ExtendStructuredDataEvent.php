<?php

declare(strict_types=1);

namespace Zeroseven\PagebasedNews\EventListener;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Zeroseven\Pagebased\Event\StructuredDataEvent;
use Zeroseven\Pagebased\Exception\TypeException;
use Zeroseven\Pagebased\Utility\CastUtility;
use Zeroseven\Pagebased\Utility\SettingsUtility;

class ExtendStructuredDataEvent
{
    protected ?ContentObjectRenderer $contentObjectRenderer = null;

    protected function getContentObjectRenderer(): ContentObjectRenderer
    {
        return $this->contentObjectRenderer ?? $this->contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    protected function forceAbsoluteUrl(mixed $parameter = null): ?string
    {
        try {
            return empty($parameter) ? null : $this->getContentObjectRenderer()->typoLink_URL([
                'parameter' => CastUtility::string($parameter),
                'forceAbsoluteUrl' => true
            ]);
        } catch (TypeException $e) {
            return null;
        }
    }

    protected function getRootPageUri(int $currentPageId): ?string
    {
        try {
            $rootPageUid = GeneralUtility::makeInstance(SiteFinder::class)?->getSiteByPageId($currentPageId)->getRootPageId();
        } catch (SiteNotFoundException $e) {
            return null;
        }

        return $this->forceAbsoluteUrl($rootPageUid);
    }

    /** @throws ResourceDoesNotExistException */
    public function __invoke(StructuredDataEvent $event): void
    {
        if (
            ($registration = $event->getRegistration())
            && ($uid = $event->getUid())
            && ($event->getRegistration()->getExtensionName() === 'pagebased_news')
            && ($article = $registration->getObject()->getRepositoryClass()->findByUid($uid))
        ) {
            $event->addPropertyType('', [
                '@context' => 'https://schema.org/',
                'headline' => $article->getTitle(),
                'url' => $this->forceAbsoluteUrl($article->getUid())
            ], 'NewsArticle');

            if ($identifier = SettingsUtility::getExtensionConfiguration($registration, 'structuredData.identifier')) {
                $event->addPropertyType('identifier', [
                    'name' => $identifier,
                    'value' => (string)$article->getUid()
                ], 'PropertyValue');
            } else {
                $settings = SettingsUtility::getExtensionConfiguration($registration);
                $settings['structuredData']['identifier'] = uniqid($registration->getExtensionName() . '-', false);

                GeneralUtility::makeInstance(ExtensionConfiguration::class)?->set($registration->getExtensionName(), $settings);
            }

            if ($image = $article->getFirstImage()) {
                $event->addProperty('image', $image);
            }

            if ($description = $article->getDescription()) {
                $event->addProperty('description', $description);
            }

            if ($dateModified = $article->getLastChangeDate() ?? $article->getCreateDate()) {
                $event->addProperty('dateModified', $dateModified->format('Y-m-d'));
            }

            if ($datePublished = ($article->getDate() ?? ($article->getAccessStartDate() ?? $article->getCreateDate()))) {
                $event->addProperty('datePublished', $datePublished->format('Y-m-d'));
            }

            if ($author = $article->getContact()) {
                $event->addPropertyType('author', [
                    'name' => $author->getFullName(),
                    'knowsAbout' => $author->getExpertise(),
                    'image' => $author->getImage(),
                    'sameAs' => [
                        $this->forceAbsoluteUrl($author->getTwitter()),
                        $this->forceAbsoluteUrl($author->getXing()),
                        $this->forceAbsoluteUrl($author->getLinkedin()),
                        $this->forceAbsoluteUrl($author->getPageLink())
                    ]
                ], 'Person');
            }
        }
    }
}
