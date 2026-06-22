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

Patch TYPO3 DataHandler
=======================

The extension stores tracking info on corresponding TYPO3 pages.
TYPO3 would copy the tracking info when copying a TYPO3 page.

It is therefore recommended to patch TYPO3 DataHandler.

You can do so by using the composer plugin
https://packagist.org/packages/cweagans/composer-patches.
Version 2.x is recommend. You can add the patch via your patches definition like
this, when following composer defaults:

.. code-block:: json

   {
       "patches": {
           "typo3/cms-core": {
               "108353 Prevent copy of Tables": "vendor/danielsiepmann/tracking/patches/typo3/cms-core/108353-prevent-copy-of-tables.patch"
           }
       }
   }

The patch might need adjustments depending on concrete patch level of your TYPO3
installation.
