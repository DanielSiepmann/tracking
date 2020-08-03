.. highlight:: bash
.. _installation:

============
Installation
============

Install the extension as usual via composer or some other way::

   composer require danielsiepmann/tracking

There is no TypoScript included which needs to be included.

Instead further configuration via :file:`Services.yaml` is necessary.
The extension highly depends on the dependency injection feature introduced with TYPO3 v10.

Ensure you have a dependency from your extension providing configuration via :file:`Services.yaml` to this extension.
Otherwise loading order might be wrong and custom configuration within :file:`Services.yaml` might not work.

Check corresponding sections about :ref:`pageview` and :ref:`recordview`.

The extension should work out of the box,
but should be configured to the specific installation.
