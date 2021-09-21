schemas
=========

.. code-block:: yaml

    # app/config/config.yaml
    solrphp_solarium:
        managed_schemas:
            -
                cores: ['default']
                unique_key: 'id'
                fields:
                    - { name: '_root_', type: 'string', doc_values: false, indexed: true, stored: false }
                    - ...
                dynamic_fields:
                    - { name: '*_txt_en_split_tight', type: 'text_en_splitting_tight', indexed: true, stored: true }
                    - ...
                copy_fields:
                    - { source: 'features', dest: 'features_str', max_chars: 256 }
                    - ...
                field_types:
                    - name: 'text_fa'
                      class: 'solr.TextField'
                      positionIncrementGap: 100
                      analyzers:
                        char_filter: {class: 'solr.PersianCharFilterFactory' }
                        tokenizer: { class: 'solr.StandardTokenizerFactory' }
                        filters:
                            - { class: 'solr.LowerCaseFilterFactory' }
                            - { class: 'solr.ArabicNormalizationFilterFactory' }
                            - { class: 'solr.PersianNormalizationFilterFactory' }
                            - { class: 'solr.StopFilterFactory', words: 'lang/stopwords_fa.txt', ignore_case: true }
                    - ...

``cores``
---------
an array of cores the schema configuration applies to.

``unique_key``
--------------
the unique key for your schema.

``fields``
----------
| the field definitions for your schema.
| all properties defined in `field type properties <https://solr.apache.org/guide/field-type-definitions-and-properties.html#field-type-properties>`_ should be available as a snake case representation.

``dynamic_fields``
------------------
| the dynamic field definitions for your schema.
|all properties defined in `field type properties <https://solr.apache.org/guide/field-type-definitions-and-properties.html#field-type-properties>`_ should be available as a snake case representation.
| **note**: be aware that the value for property ``name`` should contain a wildcard.

``copy_fields``
---------------
| the copy field definitions for your schema.

``field_types``
---------------
| the field type definitions for your schema.
| most properties defined in `field type definitions and properties <https://solr.apache.org/guide/field-type-definitions-and-properties.html>`_ are available as a snake case representation.
| **todo**: `field type similarity <https://solr.apache.org/guide/8_9/field-type-definitions-and-properties.html#field-type-similarity>`_
