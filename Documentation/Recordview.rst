.. _recordview:

==========
Recordview
==========

Many installations will have custom records beside TYPO3 pages.
E.g. one uses EXT:news or EXT:tt_address to display news or personal information.

Those typically are displayed via a Plugin content element leading to the same Page
for all records.
This part allows to track views of individual records.

All configuration happens via :ref:`t3coreapi:DependencyInjection` inside of :file:`Services.yaml` of your Sitepackage.

.. note::

   In contrast to :ref:`pageview`, there is no default rule.
   No record is tracked by default as no TYPO3 installation has any default records to track.

   In order to start tracking records, the rules need to be configured.

.. figure:: /Images/ListViewRecordviews.png
    :align: center

    Screenshot of list view of created "recordview" records.

.. figure:: /Images/RecordRecordview.png
    :align: center

    Screenshot of edit form view of created "recordview" records.

Saved record
------------

Whenever a recordview is tracked, a new record is created.
The record can be viewed via TYPO3 list module. That way all collected information can be checked.

Configure tracking
------------------

Let us examine an concrete example::

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     DanielSiepmann\Tracking\Middleware\Recordview:
       public: true
       arguments:
         $rules:
           news:
             matches: >
                 request.getQueryParams()["tx_news_pi1"] && request.getQueryParams()["tx_news_pi1"]["news"] > 0
                 and not (context.getAspect("backend.user").isLoggedIn())
                 and not (context.getAspect("frontend.preview").isPreview())
             recordUid: 'traverse(request.getQueryParams(), "tx_news_pi1", "news")'
             tableName: 'tx_news_domain_model_news'

The first paragraph will not be explained, check out :ref:`t3coreapi:configure-dependency-injection-in-extensions` instead.

The second paragraph is where the tracking is configured.
The PHP class ``DanielSiepmann\Tracking\Middleware\Recordview`` is registered as PHP middleware and will actually track the request.
Therefore this class is configured.
The only interesting argument to configure is ``$rules``.
The argument itself is an array. That way one can configure multiple rules, e.g. one per record.
The above example includes a single rule for ``topics``, but further can be added.

Each rule has the following options which are all mandatory:

``matches``
   A Symfony Expression, which is used to check whether the current rule should be processed for current request.
   Check :ref:`pageview` to get further information, as it is the same implementation and concept.

``recordUid``
   A Symfony Expression, which is used to fetch the UID of the actual record from current request.
   Only the request itself is provided within the expression.
   Check `PSR-7: HTTP message interfaces <https://www.php-fig.org/psr/psr-7/#321-psrhttpmessageserverrequestinterface>`__.

``tableName``
   A simple string which defines the actual database table name where records are stored.

Widgets
-------

The extension does not provide any widgets, but providers for widgets of EXT:dashboard.
That way widgets of EXT:dashboard can be combined with all providers of this extension.

The concepts are not documented here, check :ref:`t3dashboard:start` instead.

.. toctree::
   :glob:

   RecordviewWidgets/*
