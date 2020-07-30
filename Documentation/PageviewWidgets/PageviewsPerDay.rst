.. php:namespace:: DanielSiepmann\Tracking\Dashboard\Provider
.. program:: DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerDay

.. _pageviewsperday:

===============
PageviewsPerDay
===============

Provides the total page calls on the last x days.
This way editors can see how many total requests were made at specific dates.

Example
=======

.. figure:: /Images/Widgets/PageviewsPerDay.png
    :align: center

Default widget configuration.

:file:`Configuration/Services.yaml`::

   services:
     DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerDay:
       arguments:
         $queryBuilder: '@querybuilder.tx_tracking_pageview'
         $pagesToExclude: [1, 11, 38]

     dashboard.widget.danielsiepmann.tracking.pageViewsPerDay:
       class: 'TYPO3\CMS\Dashboard\Widgets\BarChartWidget'
       arguments:
         $view: '@dashboard.views.widget'
         $dataProvider: '@DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerDay'
       tags:
         - name: 'dashboard.widget'
           identifier: 'pageViewsBar'
           groupNames: 'tracking'
           iconIdentifier: 'content-widget-chart-bar'
           title: 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.pageViewsBar.title'
           description: 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.pageViewsBar.description'
           additionalCssClasses: 'dashboard-item--chart'
           height: 'medium'
           width: 'small'

Options
=======

.. option:: $days

   Integer defining the number of days to respect.

   Defaults to 31.

.. option:: $pagesToExclude

   Array of page uids that should not be collected.
   Defaults to empty array, all pages are shown.

   This becomes handy if certain pages are called in order to show specific records.
   In those cases the pages will be called very often but don't provide much benefit and can be excluded.
   Use this in combination with :ref:`recordview` to show the records instead.

.. option:: $dateFormat

   String defining the format used for labels.

   Defaults to 'Y-m-d'.
