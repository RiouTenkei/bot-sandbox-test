<?php

declare(strict_types=1);

namespace App\Module\Catalog\Domain;

final readonly class Supplier
{
    public function __construct(
        public string $name,
        public string $email,
        public string $phone,
    ) {
    }

    public static function acmeSupplies(): self
    {
        return new self('Acme Supplies', 'orders@acmesupplies.test', '+44 20 7946 0001');
    }

    public static function bogusWholesale(): self
    {
        return new self('Bogus Wholesale', 'sales@boguswholesale.test', '+44 20 7946 0002');
    }

    public static function demoDistributors(): self
    {
        return new self('Demo Distributors', 'hello@demodistributors.test', '+44 20 7946 0003');
    }

    public static function exampleEmporium(): self
    {
        return new self('Example Emporium', 'buy@exampleemporium.test', '+44 20 7946 0004');
    }

    public static function fixtureFoundry(): self
    {
        return new self('Fixture Foundry', 'stock@fixturefoundry.test', '+44 20 7946 0005');
    }

    public static function gizmoGoods(): self
    {
        return new self('Gizmo Goods', 'trade@gizmogoods.test', '+44 20 7946 0006');
    }

    public static function horizonHardware(): self
    {
        return new self('Horizon Hardware', 'supply@horizonhardware.test', '+44 20 7946 0007');
    }

    public static function ipsumImports(): self
    {
        return new self('Ipsum Imports', 'procure@ipsumimports.test', '+44 20 7946 0008');
    }
}
