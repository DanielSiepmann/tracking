.. highlight:: yaml
.. _pageview:

========
Pageview
========

Each view of a TYPO3 page is tracked by default.
Requests can be ignored by configuring a rule that has to match the current request.

All configuration happens via :ref:`t3coreapi:DependencyInjection` inside of :file:`Services.yaml` of your Sitepackage.

.. figure:: /Images/ListViewPageviews.png
    :align: center

    Screenshot of list view of created "pageview" records.

.. figure:: /Images/RecordPageview.png
    :align: center

    Screenshot of edit form view of created "pageview" records.

Saved record
------------

Whenever a pageview is tracked, a new record is created.
The record can be viewed via TYPO3 list module. That way all collected information can be checked.

Configure tracking
------------------

Let us examine an concrete example::

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     DanielSiepmann\Tracking\Middleware\Pageview:
       public: true
       arguments:
         $rule: >
             not (context.getAspect("backend.user").isLoggedIn())
             and not (context.getAspect("frontend.preview").isPreview())

The first paragraph will not be explained, check out :ref:`t3coreapi:configure-dependency-injection-in-extensions` instead.

The second paragraph is where the tracking is configured.
The PHP class ``DanielSiepmann\Tracking\Middleware\Pageview`` is registered as PHP middleware and will actually track the request.
Therefore this class is configured.
The only interesting argument to configure is ``$rule``,
which is a `Symfony Expression <https://symfony.com/doc/current/components/expression_language/syntax.html>`__.
The same is used by TYPO3 for TypoScript conditions and is not explained here.

This rule is evaluated to either ``true`` or ``false``,
where ``true`` means that the current request should be tracked.

The current request is available as ``Psr\Http\Message\ServerRequestInterface`` via ``request``,
while ``TYPO3\CMS\Core\Context\Context`` is available via ``context``.
That way it is possible to check all kind of information like frontend user, backend user or cookies and parameters,
as well as request header.

Check `PSR-7: HTTP message interfaces <https://www.php-fig.org/psr/psr-7/#321-psrhttpmessageserverrequestinterface>`__
as well as
:ref:`t3coreapi:context-api`.

The above example blocks tracking for requests with logged in backend user.

Widgets
-------

The extension does not provide any widgets, but providers for widgets of EXT:dashboard.
That way widgets of EXT:dashboard can be combined with all providers of this extension.

The concepts are not documented here, check :ref:`t3dashboard:start` instead.

.. toctree::
   :glob:

   PageviewWidgets/*
