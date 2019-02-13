BASE_DIR := php-cli/base
DEV_DIR := php-cli/dev

PHP_VERSIONS := 7.0 7.1 7.2 7.3
MONGO_EXT_VERSIONS := 1.3.0 1.3.4 1.4.2 1.5.3

__BASE_PHP_TARGETS := $(foreach phpver,$(PHP_VERSIONS),$(subst PHPVER,$(phpver),base-PHPVER-mongoext-MONGOVER))
__BASE_TAGS := $(foreach mongover,$(MONGO_EXT_VERSIONS),$(subst MONGOVER,$(mongover),$(__BASE_PHP_TARGETS)))

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

base.target: $(__BASE_TAGS)
	@echo "Done building"
