## [2.3.2](https://github.com/idetik/coretik-page-builder/compare/v2.3.1...v2.3.2) (2025-03-01)


### Bug Fixes

* enhance Repeater builtin feature ([9b0010a](https://github.com/idetik/coretik-page-builder/commit/9b0010a8e86d15c8457c18028c93e12cbaae8536))

## [2.3.1](https://github.com/idetik/coretik-page-builder/compare/v2.3.0...v2.3.1) (2025-02-28)


### Bug Fixes

* TypeHint library helper ([dd055bd](https://github.com/idetik/coretik-page-builder/commit/dd055bd70c84213911f63d65ac9c415a6ed61797))

# [2.3.0](https://github.com/idetik/coretik-page-builder/compare/v2.2.12...v2.3.0) (2025-02-28)


### Features

* Every block and composite may implement the ShouldBuildBlockType interface to create the block.json file used by the WordPress block editor ([#4](https://github.com/idetik/coretik-page-builder/issues/4)) ([f256f89](https://github.com/idetik/coretik-page-builder/commit/f256f891c0c4c9c544709c7bd495289999d96ace))

## [2.2.12](https://github.com/idetik/coretik-page-builder/compare/v2.2.11...v2.2.12) (2025-02-11)


### Bug Fixes

* add filter 'coretik/page-builder/in_library' Builder::library() ([91bbc06](https://github.com/idetik/coretik-page-builder/commit/91bbc064b804ff4e1e497befbf88c7afc52d3d9e))

## [2.2.11](https://github.com/idetik/coretik-page-builder/compare/v2.2.10...v2.2.11) (2025-02-11)


### Bug Fixes

* init pagebuilder library, change hook name 'coretik/page-builder/init_library' ([27b5bd7](https://github.com/idetik/coretik-page-builder/commit/27b5bd7bade50387c3401668e962265b8536b6e4))

## [2.2.10](https://github.com/idetik/coretik-page-builder/compare/v2.2.9...v2.2.10) (2024-12-12)


### Bug Fixes

* RepeaterComponent typecheck ([e034bf8](https://github.com/idetik/coretik-page-builder/commit/e034bf81bee11301736ee518291abca8424e9e30))

## [2.2.9](https://github.com/idetik/coretik-page-builder/compare/v2.2.8...v2.2.9) (2024-11-30)


### Performance Improvements

* Repeater component perf ([cfbce2a](https://github.com/idetik/coretik-page-builder/commit/cfbce2aef6dde4c876884db5234036a1952200e8))

## [2.2.8](https://github.com/idetik/coretik-page-builder/compare/v2.2.7...v2.2.8) (2024-11-03)


### Bug Fixes

* Add getItems callable on repeater components result ([41f12bd](https://github.com/idetik/coretik-page-builder/commit/41f12bdff1ef798633f070021f58ff9a74218a7d))

## [2.2.7](https://github.com/idetik/coretik-page-builder/compare/v2.2.6...v2.2.7) (2024-10-23)


### Bug Fixes

* Block __toString may not display anything ([9766ee2](https://github.com/idetik/coretik-page-builder/commit/9766ee2f1d9f76ff265a70cc8121815ef388089c))

## [2.2.6](https://github.com/idetik/coretik-page-builder/compare/v2.2.5...v2.2.6) (2024-10-16)


### Bug Fixes

* CtaComponent use link name if title not set ([eb669bb](https://github.com/idetik/coretik-page-builder/commit/eb669bb96ed78d362f362d9c11842c7d92e6c718))

## [2.2.5](https://github.com/idetik/coretik-page-builder/compare/v2.2.4...v2.2.5) (2024-10-16)


### Bug Fixes

* Better DevTools integration (requires Coretik 1.11.0) ([e18e23d](https://github.com/idetik/coretik-page-builder/commit/e18e23d82701b7133e448285375e44193a14eb67))
* Sniff ([74977d5](https://github.com/idetik/coretik-page-builder/commit/74977d513fa6a1758848b8557119d3fb01214a8f))
* Up dependencies ([cad8265](https://github.com/idetik/coretik-page-builder/commit/cad826584de88024c5807c6f70679d71cede8176))

## [2.2.4](https://github.com/idetik/coretik-page-builder/compare/v2.2.3...v2.2.4) (2024-10-15)


### Bug Fixes

* Fix column modifier ([756706c](https://github.com/idetik/coretik-page-builder/commit/756706cf36d537ee099c09d364d7f3976c6eb9b3))

## [2.2.3](https://github.com/idetik/coretik-page-builder/compare/v2.2.2...v2.2.3) (2024-08-15)


### Bug Fixes

* Fix seoSettings, force field unrequired ([13247d5](https://github.com/idetik/coretik-page-builder/commit/13247d511cba62bff26b7f57d02258e3de4a3f2b))

## [2.2.2](https://github.com/idetik/coretik-page-builder/compare/v2.2.1...v2.2.2) (2024-08-15)


### Bug Fixes

* Add config paramters and filters to composite fields groups ([07386cb](https://github.com/idetik/coretik-page-builder/commit/07386cbf822560e79206cf3bc7952563156e0b0c))

## [2.2.1](https://github.com/idetik/coretik-page-builder/compare/v2.2.0...v2.2.1) (2024-02-28)


### Bug Fixes

* Enhance composite blocks settings fields ([9c84759](https://github.com/idetik/coretik-page-builder/commit/9c84759b90ad5ab61fc77d7f925bd45fcf61ac98))

# [2.2.0](https://github.com/idetik/coretik-page-builder/compare/v2.1.3...v2.2.0) (2024-02-24)


### Bug Fixes

* Fix title component php error ([c87a514](https://github.com/idetik/coretik-page-builder/commit/c87a51458b57e0268a580b52d35b9926f8819faa))


### Features

* Populate composite block outside builder context ([bcf8db7](https://github.com/idetik/coretik-page-builder/commit/bcf8db79a88b253d8f9d3e4457f9563a28787dd5))

## [2.1.3](https://github.com/idetik/coretik-page-builder/compare/v2.1.2...v2.1.3) (2024-02-24)


### Bug Fixes

* clean & force release ([34d3153](https://github.com/idetik/coretik-page-builder/commit/34d31532a9c93e2b5f2d9aafaaac7bb5a60b28ab))

## [2.1.2](https://github.com/idetik/coretik-page-builder/compare/v2.1.1...v2.1.2) (2024-02-24)


### Bug Fixes

* bump dependencies & force publish release ([caa324c](https://github.com/idetik/coretik-page-builder/commit/caa324c54cddb55461d5e274e666e7204b1347e6))

## [2.1.1](https://github.com/idetik/coretik-page-builder/compare/v2.1.0...v2.1.1) (2024-02-24)


### Bug Fixes

* Components name & thumbnail component ([5e979e1](https://github.com/idetik/coretik-page-builder/commit/5e979e193db0445f3821568e9a40c7c4795c6e07))
* Readme wip ([32cf6cc](https://github.com/idetik/coretik-page-builder/commit/32cf6cc70a6df8513baf2d46a806d086c8e39027))

# [2.1.0](https://github.com/idetik/coretik-page-builder/compare/v2.0.4...v2.1.0) (2024-02-02)


### Bug Fixes

* license ([d6496e2](https://github.com/idetik/coretik-page-builder/commit/d6496e28fa54b386402c4ebae4e326383bacf8cd))


### Features

* Enhance CLI commands & add create-block, create-component and create-composite subcommands ([c5db2c6](https://github.com/idetik/coretik-page-builder/commit/c5db2c6b55adb3f0bdb05b2f85d6cac0891c1134))


### Performance Improvements

* Upgrade composer dependencies ([98c861a](https://github.com/idetik/coretik-page-builder/commit/98c861a2712ff6ec1090762a39ec7fbc926d57cd))

## [2.0.4](https://github.com/idetik/coretik-page-builder/compare/v2.0.3...v2.0.4) (2024-01-02)


### Bug Fixes

* relationship faker ([1a8a466](https://github.com/idetik/coretik-page-builder/commit/1a8a4661144948628dd9e455df18c116f7fc8bb9))

## [2.0.3](https://github.com/idetik/coretik-page-builder/compare/v2.0.2...v2.0.3) (2024-01-02)


### Bug Fixes

* move filesystem dependency to require ([7b7f7b1](https://github.com/idetik/coretik-page-builder/commit/7b7f7b1b07850137c10bd71cc072042ae8767cc6))

## [2.0.2](https://github.com/idetik/coretik-page-builder/compare/v2.0.1...v2.0.2) (2023-12-15)


### Bug Fixes

* Faker wysiwyg ([41e057d](https://github.com/idetik/coretik-page-builder/commit/41e057d5744414a29cec640c671f714d0d0cb61f))

## [2.0.1](https://github.com/idetik/coretik-page-builder/compare/v2.0.0...v2.0.1) (2023-12-15)


### Bug Fixes

* Debug thumbnail generator CLI ([1e296e9](https://github.com/idetik/coretik-page-builder/commit/1e296e9ecbd93631fb1b37bce3412655d0ea6f16))

# [2.0.0](https://github.com/idetik/coretik-page-builder/compare/v1.1.4...v2.0.0) (2023-12-15)


### Features

* V2 ([#3](https://github.com/idetik/coretik-page-builder/issues/3)) ([3f08372](https://github.com/idetik/coretik-page-builder/commit/3f0837284989834c47f0dfbef4a0a43c833e3f15))

## [1.1.4](https://github.com/idetik/coretik-page-builder/compare/v1.1.3...v1.1.4) (2023-10-25)


### Bug Fixes

* prevent acf module ([ca5b1c0](https://github.com/idetik/coretik-page-builder/commit/ca5b1c037d95c5b4244f9ee2e61d7cdd39dc3182))

## [1.1.3](https://github.com/idetik/coretik-page-builder/compare/v1.1.2...v1.1.3) (2023-10-25)


### Bug Fixes

* is_cli function ([eec5af8](https://github.com/idetik/coretik-page-builder/commit/eec5af86745e4298201a06401e9eb5a5d6cd7a42))

## [1.1.2](https://github.com/idetik/coretik-page-builder/compare/v1.1.1...v1.1.2) (2023-10-25)


### Bug Fixes

* handle errors if acf disabled ([20afd6f](https://github.com/idetik/coretik-page-builder/commit/20afd6f8d210e6c04914a1fd45f950de407a53d7))

## [1.1.1](https://github.com/idetik/coretik-page-builder/compare/v1.1.0...v1.1.1) (2023-10-25)


### Bug Fixes

* handle errors if acf disabled ([21d7b47](https://github.com/idetik/coretik-page-builder/commit/21d7b4799d95ef58c42ceafacb55a42f091b00d8))

# [1.1.0](https://github.com/idetik/coretik-page-builder/compare/v1.0.14...v1.1.0) (2023-10-25)


### Bug Fixes

* handle errors if acf disabled ([27f89d7](https://github.com/idetik/coretik-page-builder/commit/27f89d747809889524ffbe1b44ba262947988719))


### Features

* Enhance blocks composite / components ([6c66328](https://github.com/idetik/coretik-page-builder/commit/6c66328d53fb05208cdfb931883e5e0b10b212a5))

## [1.0.14](https://github.com/idetik/coretik-page-builder/compare/v1.0.13...v1.0.14) (2023-06-20)


### Bug Fixes

* remove Layout pageHeader block from container block ([efce10a](https://github.com/idetik/coretik-page-builder/commit/efce10aa85020bd228077220f506575c8abafc0e))

## [1.0.13](https://github.com/idetik/coretik-page-builder/compare/v1.0.12...v1.0.13) (2023-04-07)


### Bug Fixes

* Dependencies up & support PHP 8.2 ([cb42a52](https://github.com/idetik/coretik-page-builder/commit/cb42a52ab0a3e0ea2e8044567c6b6582be047a11))

## [1.0.12](https://github.com/idetik/coretik-page-builder/compare/v1.0.11...v1.0.12) (2023-01-16)


### Bug Fixes

* DI make pagebuilder service non singleton ([5e536b7](https://github.com/idetik/coretik-page-builder/commit/5e536b7f71f6a0a4b3e356b14dd25569c1c97e79))

## [1.0.11](https://github.com/idetik/coretik-page-builder/compare/v1.0.10...v1.0.11) (2022-12-23)


### Bug Fixes

* add filter hook coretik/page-builder/acf/page-builder-field/args ([e660316](https://github.com/idetik/coretik-page-builder/commit/e660316b899d7bd874dc8df4bfcfde44a81c7da1))
* add filter hook coretik/page-builder/block/fields-builder-config ([598e74e](https://github.com/idetik/coretik-page-builder/commit/598e74e4e3f8863d7428bf90f57893673499ee19))

## [1.0.10](https://github.com/idetik/coretik-page-builder/compare/v1.0.9...v1.0.10) (2022-12-22)


### Bug Fixes

* Fake field add checkbox type and datetime ([8c70f49](https://github.com/idetik/coretik-page-builder/commit/8c70f49204e4608d62ac136d9ac55cf11da13dc8))

## [1.0.9](https://github.com/idetik/coretik-page-builder/compare/v1.0.8...v1.0.9) (2022-11-20)


### Bug Fixes

* Block component cta type ([3b8eb64](https://github.com/idetik/coretik-page-builder/commit/3b8eb64c7e4771901164a1187d4d4ccd2d78c74e))

## [1.0.8](https://github.com/idetik/coretik-page-builder/compare/v1.0.7...v1.0.8) (2022-11-20)


### Bug Fixes

* Enhance Block settings ([e9385f5](https://github.com/idetik/coretik-page-builder/commit/e9385f51f4bd69fba7ebe2749543a721e3e05dfd))
* rename block settings ([3634040](https://github.com/idetik/coretik-page-builder/commit/36340405390aff9c343e075652c8d19ae8d70e0b))

## [1.0.7](https://github.com/idetik/coretik-page-builder/compare/v1.0.6...v1.0.7) (2022-11-20)


### Bug Fixes

* Block CTA component ([7af43d8](https://github.com/idetik/coretik-page-builder/commit/7af43d85e2dddf27624b45688e275d2ec7d1b2c9))

## [1.0.6](https://github.com/idetik/coretik-page-builder/compare/v1.0.5...v1.0.6) (2022-11-20)


### Bug Fixes

* Block CTA component ([033db78](https://github.com/idetik/coretik-page-builder/commit/033db789e9356934ca0f284564834e1543dbfc2a))

## [1.0.5](https://github.com/idetik/coretik-page-builder/compare/v1.0.4...v1.0.5) (2022-11-19)


### Bug Fixes

* enhance cli command && change block method category to static method ([9a86fc5](https://github.com/idetik/coretik-page-builder/commit/9a86fc5c0fe4bd0ec4718376e0a7b598cb704753))

## [1.0.4](https://github.com/idetik/coretik-page-builder/compare/v1.0.3...v1.0.4) (2022-11-19)


### Bug Fixes

* Rename WP filter coretik/page-builder/blocks to coretik/page-builder/library ([c314a8c](https://github.com/idetik/coretik-page-builder/commit/c314a8c93d4adb20a8056b53a1c6d3a7a76472a5))

## [1.0.3](https://github.com/idetik/coretik-page-builder/compare/v1.0.2...v1.0.3) (2022-11-17)


### Bug Fixes

* ThumbnailGenerator: change notice error to warning to prevent exit script in cli ([c08efce](https://github.com/idetik/coretik-page-builder/commit/c08efcec3be95cb898b69014313ff46da92cb393))

## [1.0.2](https://github.com/idetik/coretik-page-builder/compare/v1.0.1...v1.0.2) (2022-11-16)


### Bug Fixes

* ThumbnailGenerator fix set directory method ([e0ebefc](https://github.com/idetik/coretik-page-builder/commit/e0ebefcef806fc7251a7a25c86c6b7bde256f467))

## [1.0.1](https://github.com/idetik/coretik-page-builder/compare/v1.0.0...v1.0.1) (2022-11-16)


### Bug Fixes

* ACF PageBuilder field, fix php error on service call ([56e4bb7](https://github.com/idetik/coretik-page-builder/commit/56e4bb7016e13c186c1a15909ed98c794ccb65c5))

# 1.0.0 (2022-11-16)


### Features

* Configurable service & blocks cleanup ([38bd907](https://github.com/idetik/coretik-page-builder/commit/38bd907e55f33db314df16e18b6c010fe3fc1e7d))
* Coretik page builder ([6d1c09a](https://github.com/idetik/coretik-page-builder/commit/6d1c09ac45eacdf714cf0824c00dd0b01ecc0944))
* init ([ed108c6](https://github.com/idetik/coretik-page-builder/commit/ed108c68850da0fe1a8d065b74c2487bf4b695b2))
