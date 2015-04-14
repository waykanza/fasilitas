Imports System.Data.SqlClient
Imports System.IO
Imports System.Globalization

Module export_permata

    Sub Main()

        Dim app_path,
            conn_txt, status_txt, respon_txt,
            msg_respon As String

        msg_respon = ""

        app_path = My.Application.Info.DirectoryPath
        'app_path = "F:\UwAmp\www\pkb\vb\export\permata"

        conn_txt = Path.GetDirectoryName(Path.GetDirectoryName(app_path)) + "\conn.txt"
        status_txt = app_path + "\status.txt"
        respon_txt = app_path + "\respon.txt"

        If File.Exists(status_txt) = False Then
            File.AppendAllText(status_txt, "")
        End If
        If File.Exists(respon_txt) = False Then
            File.AppendAllText(respon_txt, "")
        End If
        If File.Exists(conn_txt) = False Then
            File.WriteAllText(status_txt, "FINISH")
            File.WriteAllText(respon_txt, "File conn.txt tidak ditemukan! hubungi MSI !")
            Environment.Exit(0)
        End If

        ' ============================= SET ACTION =============================

        Dim conn As New SqlConnection
        Dim cmd As New SqlCommand
        Dim row As SqlDataReader

        Dim time As DateTime = Date.Now()
        Dim periode As String = time.ToString("yyyyMM")
        Dim i As Integer = 1

        ' ============================= HERE WE GO =============================

        Try
            ' ============== STATUS & RESULT ==============

            File.WriteAllText(status_txt, "PROSES")
            File.WriteAllText(respon_txt, "")

            ' ============== DB ==============

            Dim conn_str = File.ReadAllText(conn_txt)
            conn = New SqlConnection(conn_str)
            cmd.Connection = conn
            conn.Open()

            cmd.Connection = conn
            cmd.CommandText = "" +
            "SELECT " +
                "b.NO_PELANGGAN, " +
                "(SELECT (KODE_BLOK + ' ' + NAMA_PELANGGAN) FROM KWT_PELANGGAN WHERE NO_PELANGGAN = b.NO_PELANGGAN) AS BLOK_NAPEL, " +
                "SUM(ISNULL(b.JUMLAH_AIR,0) + ISNULL(b.ABONEMEN,0) + ISNULL(b.DENDA,0) - ISNULL(DISKON_RUPIAH_AIR,0)) AS JUMLAH_AIR, " +
                "SUM(ISNULL(b.JUMLAH_IPL,0) - ISNULL(DISKON_RUPIAH_IPL,0)) AS JUMLAH_IPL " +
            "FROM " +
                "KWT_PEMBAYARAN_AI b " +
            "WHERE " +
                "b.STATUS_BAYAR IS NULL " +
            "GROUP BY b.NO_PELANGGAN " +
            "ORDER BY BLOK_NAPEL "

            row = cmd.ExecuteReader()

            ' ============== CHECK FILE ==============

            Dim save_file As String = app_path + "\files\PERMATA_EXPORT_" + periode + ".txt"

            If File.Exists(save_file) = False Then
                File.AppendAllText(save_file, "")
            Else
                File.WriteAllText(save_file, "")
            End If

            ' ============== FILL DATA ==============

            Dim NO_PELANGGAN,
                BLOK_NAPEL,
                JUMLAH_AIR,
                JUMLAH_IPL As String

            Dim TOTAL As Long = 0

            Using wr As StreamWriter = New StreamWriter(save_file)

                Do While row.Read()

                    NO_PELANGGAN = Trim(row("NO_PELANGGAN"))
                    BLOK_NAPEL = Trim(row("BLOK_NAPEL"))
                    JUMLAH_AIR = Trim(row("JUMLAH_AIR"))
                    JUMLAH_IPL = Trim(row("JUMLAH_IPL"))
                    TOTAL = CLng(JUMLAH_AIR) + CLng(JUMLAH_IPL)

                    If (BLOK_NAPEL.Length > 30) Then
                        BLOK_NAPEL = BLOK_NAPEL.Substring(0, 30)
                    End If

                    wr.WriteLine(
                        NO_PELANGGAN.PadRight(16) +
                        BLOK_NAPEL.PadRight(30) +
                        JUMLAH_AIR.PadLeft(13, "0") + "00" +
                        JUMLAH_IPL.PadLeft(13, "0") + "00" +
                        TOTAL.ToString().PadLeft(13, "0") + "00"
                    )

                Loop

                row.Close()

            End Using

        

        Finally

            conn.Close()

            File.WriteAllText(status_txt, "FINISH")
            File.WriteAllText(respon_txt, msg_respon)

            'Console.ReadLine()
            Environment.Exit(0)

        End Try

    End Sub

End Module
