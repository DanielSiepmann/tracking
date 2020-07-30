.. php:namespace:: DanielSiepmann\Tracking\Dashboard\Provider
.. program:: DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerPage

.. _pageviewsperpage:

================
PageviewsPerPage
================

Provides the total calls on a per page level.
This way editors can see which pages were requested the most during a specified period.

Example
=======

.. figure:: /Images/Widgets/PageviewsPerPage.png
    :align: center

Default widget configuration.

:file:`Configuration/Services.yaml`::

   services:
     DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerPage:
       arguments:
         $queryBuilder: '@querybuilder.tx_tracking_pageview'
         $pagesToExclude: [1, 11, 38]

     dashboard.widget.danielsiepmann.tracking.pageViewsPerPage:
       class: 'TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget'
       arguments:
         $view: '@dashboard.views.widget'
         $dataProvider: '@DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerPage'
       tags:
         - name: 'dashboard.widget'
           identifier: 'pageViewsPerPageDoughnut'
           groupNames: 'tracking'
           iconIdentifier: 'content-widget-chart-bar'
           title: 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.pageViewsPerPageDoughnut.title'
           description: 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.pageViewsPerPageDoughnut.description'
           additionalCssClasses: 'dashboard-item--chart'
           height: 'medium'
           width: 'small'

Options
=======

.. option:: $days

   Integer defining the number of days to respect.

   Defaults to 31.

.. option:: $maxResults

   Integer defining how many pages should be shown.
   Defaults to 6 because EXT:dashboard only provides 6 colors.

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
