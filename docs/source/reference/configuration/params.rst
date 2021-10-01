parameters
==========

.. code-block:: yaml

    parameters:
        -
          cores:
            - foo
          parameter_set_maps:
            - name: 'foo'
              parameters:
                - { name: 'foo', value: 'bar'}
              _invariants_:
                - { name: 'baz', value: 'qux' }
              _appends_:
                - { name: 'quux', value: 'foo' }

``cores``
---------
| an array of cores the solr configuration applies to.

``parameter_set_maps``
----------------------
| the `maps <https://solr.apache.org/guide/request-parameters-api.html#configuring-request-parameters>`_ for your configuration