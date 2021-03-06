.. php:namespace:: DanielSiepmann\Tracking\Dashboard\Provider
.. program:: DanielSiepmann\Tracking\Dashboard\Provider\NewestPageviews

.. _newestpageviews:

===============
NewestPageviews
===============

Provides a list of the newest pageview entries.

Example
=======

.. figure:: /Images/Widgets/NewestPageviews.png
    :align: center

Default widget configuration.

:file:`Configuration/Services.yaml`::

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     DanielSiepmann\Tracking\Dashboard\Provider\NewestPageviews:
       arguments:
         $queryBuilder: '@querybuilder.tx_tracking_pageview'
         $pagesToExclude: [1, 11, 38]

     dashboard.widget.danielsiepmann.tracking.newestPageviews:
       class: 'TYPO3\CMS\Dashboard\Widgets\ListWidget'
       arguments:
         $view: '@dashboard.views.widget'
         $dataProvider: '@DanielSiepmann\Tracking\Dashboard\Provider\NewestPageviews'
       tags:
         - name: 'dashboard.widget'
           identifier: 'newestPageviewsList'
           groupNames: 'tracking'
           iconIdentifier: 'content-widget-list'
           title: 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.newestPageviewsList.title'
           description: 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.newestPageviewsList.description'
           height: 'medium'
           width: 'small'

Options
=======

.. option:: $maxResults

   Integer defining how many results should be displayed.
   Defaults to 6.

.. option:: $pagesToExclude

   Array of page uids that should not be collected.
   Defaults to empty array, all pages are shown.

   This becomes handy if certain pages are called in order to show specific records.
   In those cases the pages will be called very often but don't provide much benefit and can be excluded.
   Use this in combination with :ref:`recordview` to show the records instead.

.. option:: $languageLimitation

   Array of ``sys_language_uid``'s to include.
   Defaults to empty array, all languages are shown.

   Allows to limit results to specific lanuages.
   All entries tracked when visiting page with this language are shown.
   If multiple languages are shown, default system language labels are used.
   If only a single lanugage is allowed, record labels are translated to that language.
