
Imports System.Data.SqlClient
Imports System.IO
Imports System.Globalization

Module export_sm

    Sub Main()

        Dim app_path,
            conn_txt, status_txt,
            periode_txt, respon_txt,
            export_data, msg_respon As String

        export_data = ""
        msg_respon = ""

        app_path = My.Application.Info.DirectoryPath
        'app_path = "F:\UwAmp\www\pkb\vb\export\sm"

        conn_txt = Path.GetDirectoryName(Path.GetDirectoryName(app_path)) + "\conn.txt"
        status_txt = app_path + "\status.txt"
        periode_txt = app_path + "\periode.txt"
        respon_txt = app_path + "\respon.txt"

        If File.Exists(status_txt) = False Then
            File.AppendAllText(status_txt, "")
        End If
        If File.Exists(periode_txt) = False Then
            File.AppendAllText(periode_txt, "")
        End If
        If File.Exists(respon_txt) = False Then
            File.AppendAllText(respon_txt, "")
        End If
        If File.Exists(conn_txt) = False Then
            File.WriteAllText(status_txt, "FINISH")
            File.WriteAllText(respon_txt, "File conn.txt tidak ditemukan! hubungi MSI !")
            Environment.Exit(0)
        End If

        Dim conn As New SqlConnection
        Dim cmd As New SqlCommand
        Dim row As SqlDataReader

        Dim periode, periode_m1, periode_m2, periode_m3 As String

        Try

            ' SET STATUS AND CLEAN RESULT

            File.WriteAllText(status_txt, "PROSES")
            File.WriteAllText(respon_txt, "")

            ' VARIABLE PERIODE

            periode = File.ReadAllText(periode_txt)

            If String.IsNullOrEmpty(periode) Or periode.Length <> 6 Then
                Throw New Exception("Periode tidak valid : " + periode)
            End If

            Dim time As DateTime = Date.ParseExact(periode, "yyyyMM", CultureInfo.InvariantCulture)
            periode = time.ToString("yyyyMM")
            periode_m1 = time.AddMonths(-1).ToString("yyyyMM")
            periode_m2 = time.AddMonths(-2).ToString("yyyyMM")
            periode_m3 = time.AddMonths(-3).ToString("yyyyMM")

            Dim conn_str = File.ReadAllText(conn_txt)
            conn = New SqlConnection(conn_str)
            cmd.Connection = conn
            conn.Open()

            cmd.CommandText = "" +
"DECLARE " +
"@tab AS VARCHAR(10) = CHAR(9), " +
"@periode AS VARCHAR(10) = '" + periode + "', " +
"@periode_m1 AS VARCHAR(10) = '" + periode_m1 + "', " +
"@periode_m2 AS VARCHAR(10) = '" + periode_m2 + "', " +
"@periode_m3 AS VARCHAR(10) = '" + periode_m3 + "' " +
"SELECT " +
"( " +
    "SELECT " +
    "( " +
        "q.NO_PELANGGAN + @tab + " +
        "ISNULL(t.KODE_BLOK, '') + @tab + " +
        "ISNULL(t.NAMA_PELANGGAN, '') + @tab + " +
        "ISNULL(t.NO_TELEPON, '') + @tab + " +
        "ISNULL(t.NO_HP, '') + @tab + " +
        "ISNULL(t.KODE_SEKTOR, '') + @tab + " +
        "ISNULL(t.KODE_CLUSTER, '') + @tab + " +
        "ISNULL(t.KEY_AIR, '') + @tab + " +
        "CAST(SUM(q.S1) AS VARCHAR) + @tab + " +
        "CAST(SUM(q.S2) AS VARCHAR) + @tab + " +
        "CAST(SUM(q.S3) AS VARCHAR) + @tab + " +
        "CAST(SUM(q.P1) AS VARCHAR) + @tab + " +
        "CAST(SUM(q.P2) AS VARCHAR) + @tab + " +
        "CAST(SUM(q.P3) AS VARCHAR) + @tab + " +
        "'1' " +
    ") " +
    "FROM " +
        "KWT_PELANGGAN t " +
        "JOIN KWT_PEMBAYARAN_AI u ON t.NO_PELANGGAN = u.NO_PELANGGAN " +
    "WHERE " +
        "u.TRX IN ('1', '2', '4', '5') AND u.AKTIF_AIR = '1' AND " +
        "u.PERIODE = @periode AND " +
        "u.NO_PELANGGAN = q.NO_PELANGGAN " +
") AS LINE " +
"FROM " +
"( " +
    "SELECT " +
        "x.NO_PELANGGAN AS NO_PELANGGAN, " +
        "(CASE WHEN x.PERIODE = @periode_m1 THEN x.STAND_AKHIR ELSE 0 END) AS S1, " +
        "(CASE WHEN x.PERIODE = @periode_m2 THEN x.STAND_AKHIR ELSE 0 END) AS S2, " +
        "(CASE WHEN x.PERIODE = @periode_m3 THEN x.STAND_AKHIR ELSE 0 END) AS S3, " +
        "0 AS P1, " +
        "(CASE WHEN x.PERIODE = @periode_m1 THEN (x.STAND_AKHIR - x.STAND_LALU) ELSE 0 END) AS P2, " +
        "(CASE WHEN x.PERIODE = @periode_m2 THEN (x.STAND_AKHIR - x.STAND_LALU) ELSE 0 END) AS P3 " +
    "FROM " +
        "KWT_PELANGGAN y " +
        "JOIN KWT_PEMBAYARAN_AI x ON y.NO_PELANGGAN = x.NO_PELANGGAN " +
    "WHERE " +
        "x.TRX IN ('1', '2', '4', '5') AND x.AKTIF_AIR = '1' AND " +
        "x.PERIODE IN (@periode_m1,@periode_m2,@periode_m3) AND " +
        "x.NO_PELANGGAN IN " +
        "( " +
            "SELECT p.NO_PELANGGAN " +
            "FROM " +
                "KWT_PELANGGAN o " +
                "JOIN KWT_PEMBAYARAN_AI p ON o.NO_PELANGGAN = p.NO_PELANGGAN " +
            "WHERE " +
                "p.TRX IN ('1', '2', '4', '5') AND p.AKTIF_AIR = '1' AND " +
                "p.PERIODE = @periode " +
        ") " +
    "UNION ALL " +
    "SELECT " +
        "b.NO_PELANGGAN AS NO_PELANGGAN, " +
        "0 AS S1, " +
        "0 AS S2, " +
        "0 AS S3, " +
        "(b.STAND_AKHIR - b.STAND_LALU) AS P1, " +
        "0 AS P2, " +
        "0 AS P3 " +
    "FROM " +
        "KWT_PELANGGAN a " +
        "JOIN KWT_PEMBAYARAN_AI b ON a.NO_PELANGGAN = b.NO_PELANGGAN " +
    "WHERE " +
        "b.TRX IN ('1', '2', '4', '5') AND b.AKTIF_AIR = '1' AND " +
        "b.PERIODE = @periode " +
") q " +
"GROUP BY q.NO_PELANGGAN " +
"ORDER BY LINE ASC"

            Dim save_file As String = app_path + "\files\BIMASAKTI_EXPORT_" + periode + ".txt"

            If File.Exists(save_file) = False Then
                File.AppendAllText(save_file, "")
            Else
                File.WriteAllText(save_file, "")
            End If

            row = cmd.ExecuteReader()

            Using wr As StreamWriter = New StreamWriter(save_file)

                Do While row.Read()

                    wr.WriteLine(row("LINE"))

                Loop

                row.Close()

            End Using

        Catch ex As NullReferenceException

            ' SKIP FOR THIS ERROR

        Catch ex As Exception

            msg_respon = ex.Message + vbNewLine

        Finally

            conn.Close()

            File.WriteAllText(status_txt, "FINISH")
            File.WriteAllText(periode_txt, "")
            File.WriteAllText(respon_txt, msg_respon)

            'Console.ReadLine()
            Environment.Exit(0)

        End Try

    End Sub

End Module
