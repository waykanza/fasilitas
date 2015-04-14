DECLARE
   @tab AS VARCHAR(10) = CHAR(9),
   @periode AS VARCHAR(10) = ' + periode + ',
   @periode_m1 AS VARCHAR(10) = ' + periode_m1 + ',
   @periode_m2 AS VARCHAR(10) = ' + periode_m2 + ',
   @periode_m3 AS VARCHAR(10) = ' + periode_m3 + '
   SELECT
   (
  SELECT
  (
   q.NO_PELANGGAN + @tab +
   ISNULL(t.KODE_BLOK, '') + @tab +
   ISNULL(t.NAMA_PELANGGAN, '') + @tab +
   ISNULL(t.NO_TELEPON, '') + @tab +
   ISNULL(t.NO_HP, '') + @tab +
   ISNULL(u.KODE_SEKTOR, '') + @tab +
   ISNULL(u.KODE_CLUSTER, '') + @tab +
   ISNULL(u.KEY_AIR, '') + @tab +
   CAST(SUM(q.S1) AS VARCHAR) + @tab +
   CAST(SUM(q.S2) AS VARCHAR) + @tab +
   CAST(SUM(q.S3) AS VARCHAR) + @tab +
   CAST(SUM(q.P1) AS VARCHAR) + @tab +
   CAST(SUM(q.P2) AS VARCHAR) + @tab +
   CAST(SUM(q.P3) AS VARCHAR) + @tab +
   '1'
  )
  FROM
   KWT_PELANGGAN t
   JOIN KWT_PEMBAYARAN u ON t.NO_PELANGGAN = u.NO_PELANGGAN
  WHERE
   u.PERIODE = @periode AND
   u.NO_PELANGGAN = q.NO_PELANGGAN
 ) AS LINE
   FROM
   (
    SELECT
     x.NO_PELANGGAN AS NO_PELANGGAN,
     (CASE WHEN x.PERIODE = @periode_m1 THEN x.STAND_AKHIR ELSE 0 END) AS S1,
     (CASE WHEN x.PERIODE = @periode_m2 THEN x.STAND_AKHIR ELSE 0 END) AS S2,
     (CASE WHEN x.PERIODE = @periode_m3 THEN x.STAND_AKHIR ELSE 0 END) AS S3,
     0 AS P1,
     (CASE WHEN x.PERIODE = @periode_m1 THEN (x.STAND_AKHIR - x.STAND_LALU) ELSE 0 END) AS P2,
     (CASE WHEN x.PERIODE = @periode_m2 THEN (x.STAND_AKHIR - x.STAND_LALU) ELSE 0 END) AS P3
    FROM
     KWT_PELANGGAN y
     JOIN KWT_PEMBAYARAN x ON y.NO_PELANGGAN = x.NO_PELANGGAN
    WHERE
     x.PERIODE IN (@periode_m1,@periode_m2,@periode_m3) AND
     x.NO_PELANGGAN IN
     (
      SELECT p.NO_PELANGGAN
      FROM
       KWT_PELANGGAN o
       JOIN KWT_PEMBAYARAN p ON o.NO_PELANGGAN = p.NO_PELANGGAN
      WHERE p.PERIODE = @periode
     )
    UNION ALL
    SELECT
     b.NO_PELANGGAN AS NO_PELANGGAN,
     0 AS S1,
     0 AS S2,
     0 AS S3,
     (b.STAND_AKHIR - b.STAND_LALU) AS P1,
     0 AS P2,
     0 AS P3
    FROM
     KWT_PELANGGAN a
     JOIN KWT_PEMBAYARAN b ON a.NO_PELANGGAN = b.NO_PELANGGAN
    WHERE b.PERIODE = @periode
   ) q
   GROUP BY q.NO_PELANGGAN
   ORDER BY LINE ASC