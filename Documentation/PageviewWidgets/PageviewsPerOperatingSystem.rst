.. php:namespace:: DanielSiepmann\Tracking\Dashboard\Provider
.. program:: DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerOperatingSystem

.. _pageviewsperoperatingsystem:

===========================
PageviewsPerOperatingSystem
===========================

Provides the total calls on a operating system level.
This way editors can see which operating systems most visitors use.

Example
=======

.. figure:: /Images/Widgets/PageviewsPerOperatingSystem.png
    :align: center

Default widget configuration.

:file:`Configuration/Services.yaml`::

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerOperatingSystem:
       arguments:
         $queryBuilder: '@querybuilder.tx_tracking_pageview'
         $days: 62

     dashboard.widget.danielsiepmann.tracking.operatingSystems:
       class: 'TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget'
       arguments:
         $view: '@dashboard.views.widget'
         $dataProvider: '@DanielSiepmann\Tracking\Dashboard\Provider\PageviewsPerOperatingSystem'
       tags:
         - name: 'dashboard.widget'
           identifier: 'operatingSystemsDoughnut'
           groupNames: 'tracking'
           iconIdentifier: 'content-widget-chart-pie'
           title: 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.operatingSystemsDoughnut.title'
           description: 'LLL:EXT:tracking/Resources/Private/Language/locallang.xlf:dashboard.widgets.operatingSystemsDoughnut.description'
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
