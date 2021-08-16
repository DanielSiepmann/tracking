.. _tags:

Tags
=====

Tags are attached to all tracking information like :ref:`pageview` and :ref:`recordview`.
An example for a single record would be: ``bot:"yes",bot_name:"Slack",os:"Unkown"``.

Tags are extracted whenever a new record is saved, also during :ref:`updateExistingRecords`.

The extension provides some extractors to attach tags out of the box.
Further can be provided by foreign extensions or sites.
Each extractor has to implement either ``DanielSiepmann\Tracking\Domain\Extractors\PageviewExtractor`` and \ or ``DanielSiepmann\Tracking\Domain\Extractors\RecordviewExtractor`` interface.

This allows to add arbitrary data as tags to each tracking record.
Those can then be used to generate reports or build widgets.

Existing extractors
-------------------

The following are provided out of the box.
One can replace them using :file:`Services.yaml`.

Operating System
^^^^^^^^^^^^^^^^

Contains old logic to detect operating system of requests.
The operating system is added as ``os`` tag, e.g.: ``os:"Macintosh"``.

Bots
^^^^

Contains old logic to detect bots of requests.
The bot is added either as ``bot:"no"``.
If a bot is detected it is added as ``bot:"yes"`` combined with its name ``bot_name:"Slack"``.
