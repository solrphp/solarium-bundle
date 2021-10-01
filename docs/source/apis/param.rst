Request Parameters API
======================
| see the `official Request Parameters API reference <https://solr.apache.org/guide/8_10/request-parameters-api.html>`_ for the api's description.

.. |api-manager| replace:: ``ParamManager``

.. include:: ../partials/api-manager.rstinc

.. include:: ../partials/config-handler.rstinc

.. include:: ../partials/console-commands.rstinc

update
~~~~~~
the params update is available by running

.. code-block:: bash

    $ php bin/console solr:param:update <core-name>

this command compares the properties configured under ``parameters`` for given core name and syncs the ``params.json`` accordingly.

