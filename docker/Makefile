BASE_DIR := php-cli/base
DEV_DIR := php-cli/dev

# See
# https://docs.mongodb.com/ecosystem/drivers/driver-compatibility-reference/#reference-compatibility-language-php
TARGETS := 	base-7.0-mongoext-1.1.5 \
			base-7.1-mongoext-1.1.5 \
			base-7.0-mongoext-1.2.0 \
			base-7.1-mongoext-1.2.0 \
			base-7.0-mongoext-1.3.0 \
			base-7.1-mongoext-1.3.0 \
			base-7.2-mongoext-1.3.0 \
			base-7.0-mongoext-1.3.4 \
			base-7.1-mongoext-1.3.4 \
			base-7.2-mongoext-1.3.4 \
			base-7.0-mongoext-1.4.2 \
			base-7.1-mongoext-1.4.2 \
			base-7.2-mongoext-1.4.2 \
			base-7.0-mongoext-1.5.3 \
			base-7.1-mongoext-1.5.3 \
			base-7.2-mongoext-1.5.3 \
			base-7.3-mongoext-1.5.3

usage:
	@echo "Select one target"

base-%: PHP_VERSION = $(word 1,$(subst -, ,$*))
base-%: MONGO_EXT_VERSION = $(word 3,$(subst -, ,$*))
base-%: IMAGE_URI = ilariopierbattista/mongodb-bundle-php:base-$*
base-%:
	@echo "PHP version: $(PHP_VERSION)"
	@echo "MongoDB extension version: $(MONGO_EXT_VERSION)"
	@echo "Image: $(IMAGE_URI)"
	docker build \
		--build-arg PHP_VERSION=$(PHP_VERSION) \
		--build-arg MONGODB_EXTENSION_VERSION=$(MONGO_EXT_VERSION) \
		-t $(IMAGE_URI) \
		$(BASE_DIR)
	docker push $(IMAGE_URI)

base.target: $(TARGETS)
	@echo "Done building"
