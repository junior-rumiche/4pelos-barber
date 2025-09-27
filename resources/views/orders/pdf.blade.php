<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Orden #{{ sprintf('%04d', $order->id) }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: 'Inter', Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 24px 28px;
            font-size: 12px;
            color: #111827;
            background-color: #ffffff;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 16px;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px;
        }

        h2 {
            font-size: 14px;
            margin: 0 0 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 4px;
        }

        td {
            padding: 8px 4px;
            vertical-align: top;
            border-bottom: 1px solid #f3f4f6;
        }

        .meta {
            margin-bottom: 12px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #ffffff;
        }

        .badge-warning {
            background-color: #f59e0b;
        }

        .badge-info {
            background-color: #3b82f6;
        }

        .badge-success {
            background-color: #10b981;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .totals {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }

        .totals table {
            width: auto;
        }

        .text-right {
            text-align: right;
        }

        .small {
            font-size: 11px;
            color: #6b7280;
        }

        footer {
            margin-top: 24px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <div>
            <h1>Orden #{{ sprintf('%04d', $order->id) }}</h1>
            <p class="small">Generada el {{ now()->format('d/m/Y h:i A') }}</p>
        </div>
        <div class="meta">
            @php
            $statusColors = [
            \App\Models\Order::STATUS_PENDING => 'badge-warning',
            \App\Models\Order::STATUS_IN_PROGRESS => 'badge-info',
            \App\Models\Order::STATUS_PAID => 'badge-success',
            ];
            @endphp
            <span class="badge {{ $statusColors[$order->status] ?? 'badge-info' }}">
                {{ \App\Models\Order::STATUS_LABELS[$order->status] ?? 'Desconocido' }}
            </span>
        </div>
    </header>

    <section class="grid">
        <article>
            <h2>Cliente</h2>
            <p><strong>{{ $order->customer?->full_name ?? 'No asignado' }}</strong></p>
            <p class="small">
                Teléfono: {{ $order->customer?->phone ?? 'No disponible' }}
            </p>
        </article>

        <article>
            <h2>Detalles</h2>
            <p class="small">
                Creado: {{ optional($order->created_at)->format('d/m/Y h:i A') ?? 'No disponible' }}<br>
                Registrado por: {{ $order->createdBy?->name ?? 'No disponible' }}<br>
                Pagado en: {{ optional($order->paid_at)->format('d/m/Y h:i A') ?? 'Pendiente' }}<br>
                Procesado por: {{ $order->paymentProcessedBy?->name ?? 'No disponible' }}
            </p>
        </article>
    </section>

    <section style="margin-top: 20px;">
        <h2>Servicios</h2>
        <table>
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Cantidad</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->service?->name ?? 'Servicio eliminado' }}</strong><br>
                        <span class="small">{{ $item->service?->description ?? '' }}</span>
                    </td>
                    <td class="text-right">S/ {{ number_format($item->price_at_time_of_order, 2) }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">S/ {{ number_format($item->price_at_time_of_order * $item->quantity, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-right small">No se registraron servicios para esta orden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="totals">
        <table>
            <tbody>
                <tr>
                    <td class="text-right"><strong>Total</strong></td>
                    <td class="text-right" style="min-width: 120px;">S/ {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </section>

    <footer>
        Documento generado automáticamente · {{ config('app.name') }}
    </footer>
</body>

</html>