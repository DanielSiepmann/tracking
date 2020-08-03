.. php:namespace:: DanielSiepmann\Tracking\Dashboard\Provider
.. program:: DanielSiepmann\Tracking\Dashboard\Provider\Recordviews

.. _recordviews:

===========
Recordviews
===========

Provides the total views of configured records.
This way editors can see which records were requested the most during a specified period.

Example
=======

.. figure:: /Images/Widgets/Recordviews.png
    :align: center

.. note::

   In contrast to :ref:`pageview`, there is no default rule.
   No record is tracked by default as no TYPO3 installation has any default records to track.

   In order to start tracking records, the rules need to be configured.

Example widget configuration.

:file:`Configuration/Services.yaml`::

   services:
     dashboard.provider.danielsiepmann.tracking.records.news:
       class: 'DanielSiepmann\Tracking\Dashboard\Provider\Recordviews'
       arguments:
         $queryBuilder: '@querybuilder.tx_tracking_recordview'
         $recordTableLimitation: ['tx_news_domain_model_news']

     dashboard.widget.danielsiepmann.tracking.records.news:
       class: 'TYPO3\CMS\Dashboard\Widgets\DoughnutChartWidget'
       arguments:
         $view: '@dashboard.views.widget'
         $dataProvider: '@dashboard.provider.danielsiepmann.tracking.records.news'
       tags:
         - name: 'dashboard.widget'
           identifier: 'newsDoughnut'
           groupNames: 'tracking'
           iconIdentifier: 'content-widget-chart-pie'
           title: 'News'
           description: 'Shows which news are called most'
           additionalCssClasses: 'dashboard-item--chart'
           height: 'medium'
           width: 'small'

Each widget should be a combination of an configured provider as well as an widget from EXT:dashboard.
The provider delivers results for all chart widgets.

The above example configures the provider first,
followed by an widget using the provider to display top topics.

Only the provider is documented, as the widget is part of EXT:dashboard.

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

   This can be used if records are delivered through different pages.
   This way news records can be filtered e.g. by limiting to press or internal news plugin pages.

.. option:: $recordTableLimitation

   Array of database table names.
   Defaults to empty array, records from all tables are shown.

   Allows to limit the resulting records to specific tables.
   E.g. only show records of ``sys_category`` or ``tt_address``.

.. option:: $recordTypeLimitation

   Array of record types.
   Defaults to empty array, records of all types are shown.

   TYPO3 allows to define a types field per database table.
   E.g. ``doktype`` for ``pages`` table, or ``CType`` for ``tt_content``.
   That way different sub types of the same record can be stored.

   Using this option offers a way to limit records e.g. to specific types of news or
   address records.

.. option:: $languageLimitation

   Array of ``sys_language_uid``'s to include.
   Defaults to empty array, all languages are shown.

   Allows to limit results to specific lanuages.
   All entries tracked when visiting page with this language are shown.
   If multiple languages are shown, default system language labels are used.
   If only a single lanugage is allowed, record labels are translated to that language.
