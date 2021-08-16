.. _start:

==================
Tracking Extension
==================

A very simple server side tracking extension.
Initially developed for demonstration purposes.
Soon requested by customers in order to have privacy focused minimal tracking inside of TYPO3.

The extension provides built in tracking.
All requests are tracked right within TYPO3 as custom records.

The extension also delivers widgets for EXT:dashboard to visualize tracked information.

.. _goal:

Goal
----

This extension only provides very basic features and is not intended to resolve complex solutions like Google Analytics.
Yet it should provide the very minimum requirements to remove the need of such complex solutions on small websites.

.. figure:: /Images/Widgets.png
    :align: center

    Figure 1-1: Screenshot of how widgets might look, representing tracked information.

.. figure:: /Images/ListViewPageviews.png
    :align: center

    Figure 1-2: Screenshot of TYPO3 list view, showing records of tracked data.

Integrators should be able to configure more or less everything.
From collection to displaying via widgets.

Features
--------

The extension allows to track :ref:`pageview`,
as well as views to specific TYPO3 records via :ref:`recordview`,
e.g. records from EXT:news or EXT:tt_address.

Each of them can be extended with arbitrary tags extracted from request.

Missing features
----------------

Features that will not make it:

The extension does not limit reporting based on user access.
Foreign systems like Google Analytics also wouldn't know which pages or records an editor has access to.

Features that might be implemented in the future:

* Remove tracked records based on adjusted rule.
  Right now results tracked will be kept.
  If rules are adjusted, e.g. another bot is excluded, old entries will be kept.
  In the future there might be an command that will reduce existing records based on current rules.

* Collecting information about referrers.

* Collecting information based on Events.

* Collecting information about URL parameters like campaigns or utm.

* Does not extract device types out of User Agents.

* Does not extract version of operating system.

* Has a very rough bot detection.

Differences to typical tracking solutions
-----------------------------------------

This extension does not need any JavaScript or Cookies.
Tracking is happening on server side.

Therefore Client caching can prevent "requests" from being tracked.
But that can be seen as an "unique visitor" feature.

Also information like "how long did the user visit the page" are not available.

Therefore no data is passed to any 3rd Party or kept and made available.
Only internal information of TYPO3, such as Page or records, are tracked.
The only foreign information being tracked is the User Agent and URL,
in order to extract further information from them with future updates.

.. toctree::
   :hidden:

   Installation
   Pageview
   Recordview
   Tags
   UpdateExistingRecords
   Changelog
