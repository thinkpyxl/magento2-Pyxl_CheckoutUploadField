define([
    'Magento_Checkout/js/view/payment/default',
    'uiLayout',
], function (Component, layout) {
    'use strict';

    return function (Component) {
        return Component.extend({
            defaults: {
                template: 'Pyxl_CheckoutUploadField/payment/purchaseorder-form',
                purchaseOrderNumber: '',
                purchaseOrderFilename: '',
                purchaseOrderContact: ''
            },

            /** @inheritdoc */
            initObservable: function () {
                this._super()
                    .observe('purchaseOrderNumber');

                this._super()
                    .observe('purchaseOrderFilename');

                this._super()
                    .observe('purchaseOrderContact');

                return this;
            },

            /**
             * Initialize child elements
             *
             * @returns {Component} Chainable.
             */
            initChildren: function () {
                this.createFileUploaderComponent();

                return this;
            },

            /**
             * Create child file uploader component
             *
             * @returns {Component} Chainable.
             */
            createFileUploaderComponent: function () {
                var self = this;

                var fileUploaderComponent = {
                    parent: this.name,
                    name: this.name + '.uploader',
                    label: 'Select file to upload',
                    allowedExtensions: 'jpg jpeg png pdf doc docx',
                    placeholderType: 'image',
                    component: 'Magento_Ui/js/form/element/file-uploader',
                    template: 'Pyxl_CheckoutUploadField/uploader',
                    previewTmpl: 'Pyxl_CheckoutUploadField/preview',
                    displayArea: 'uploader',
                    uploaderConfig: {url: '/uploader/file/index'},
                    required: true,
                    addFile: function(file) {
                        file = this.processFile(file);

                        this.isMultipleFiles ?
                            this.value.push(file) :
                            this.value([file]);

                        self.purchaseOrderFilename(file.name);

                        return this;
                    }
                };

                layout([fileUploaderComponent]);

                return this;
            },

            /**
             * @return {Object}
             */
            getData: function () {
                return {
                    method: this.item.method,
                    'po_number': this.purchaseOrderNumber(),
                    'additional_data': {
                        'po_filename': this.purchaseOrderFilename(),
                        'po_contact': this.purchaseOrderContact()
                    }
                };
            },
        });
    }
});