.. php:namespace:: DanielSiepmann\Tracking\Dashboard\Provider
.. class:: PageviewsPerDay

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

:file:`Configuration/Services.yaml`:

.. code-block:: yaml

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

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
           height: 'medium'
           width: 'small'

Options
=======

..  confval:: $days
    :name: pageViewsPerDay-days
    :required: false
    :type: Integer
    :default: 31

..  confval:: $pagesToExclude
    :name: pageViewsPerDay-pagesToExclude
    :required: false
    :type: array of page UIDs
    :default: empty array, all pages are shown.

    This becomes handy if certain pages are called in order to show specific records.
    In those cases the pages will be called very often but don't provide much benefit and can be excluded.
    Use this in combination with :ref:`recordview` to show the records instead.

..  confval:: $dateFormat
    :name: pageViewsPerDay-dateFormat
    :required: false
    :type: string
    :default: ``Y-m-d``

    Defines format used for labels.

..  confval:: $languageLimitation
    :name: pageViewsPerDay-languageLimitation
    :required: false
    :type: array of ``sys_language_uid``'s to include
    :default: empty array, all languages are shown

    Allows to limit results to specific lanuages.
    All entries tracked when visiting page with this language are shown.
    If multiple languages are shown, default system language labels are used.
    If only a single lanugage is allowed, record labels are translated to that language.
