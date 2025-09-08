@extends('customer.layout')

@section('content')
  <h2>Cari Layanan</h2>
  <p>Gunakan fitur pencarian untuk menemukan layanan yang kamu butuhkan.</p>

  <form method="GET" action="#">
    <input type="text" name="keyword" placeholder="Cari layanan..." style="padding: 8px; width: 300px;">
    <button type="submit" style="padding: 8px 15px;">Cari</button>
  </form>
@endsection
