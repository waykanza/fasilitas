Imports System.Data.SqlClient
Imports System.IO
Imports System.Globalization

Module export_niaga_ad

    Private Function get_string_bulan(ByVal bulan As String) As String
        Dim b As Integer
        Dim r As String = ""
        b = CInt(bulan)
        Select Case b
            Case 1
                r = "JANUARY"
            Case 2
                r = "FEBRUARY"
            Case 3
                r = "MARCH"
            Case 4
                r = "APRIL"
            Case 5
                r = "MEY"
            Case 6
                r = "JUNE"
            Case 7
                r = "JULY"
            Case 8
                r = "AUGUST"
            Case 9
                r = "SEPTEMBER"
            Case 10
                r = "OCTOBER"
            Case 11
                r = "NOVEMBER"
            Case 12
                r = "DECEMBER"
        End Select

        Return r
    End Function

    Sub Main()

        Dim app_path,
            conn_txt, status_txt, respon_txt,
            msg_respon As String

        msg_respon = ""

        app_path = My.Application.Info.DirectoryPath
        'app_path = "F:\UwAmp\www\pkb\vb\export\niaga_ad"

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
                "(SELECT NO_REKENING FROM KWT_PELANGGAN WHERE NO_PELANGGAN = b.NO_PELANGGAN) AS NO_REKENING, " +
                "SUM( (ISNULL(b.JUMLAH_AIR,0) + ISNULL(b.ABONEMEN,0) + ISNULL(b.DENDA,0) + ISNULL(b.JUMLAH_IPL,0)) - (ISNULL(DISKON_RUPIAH_AIR,0) + ISNULL(DISKON_RUPIAH_IPL,0)) ) AS JUMLAH_BAYAR " +
            "FROM " +
                "KWT_PEMBAYARAN_AI b " +
                "JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN " +
            "WHERE " +
                "p.DEBET_BANK = '1' AND " +
                "p.KODE_BANK = 'BN'AND " +
                "p.NO_REKENING IS NOT NULL AND " +
                "b.STATUS_BAYAR IS NULL " +
            "GROUP BY b.NO_PELANGGAN "

            row = cmd.ExecuteReader()

            ' ============== CHECK FILE ==============

            Dim save_file_txt As String = app_path + "\files\NIAGA_AD_EXPORT_" + periode + ".txt"

            If File.Exists(save_file_txt) = False Then
                File.AppendAllText(save_file_txt, "")
            Else
                File.WriteAllText(save_file_txt, "")
            End If

            ' ============== FILL DATA ==============

            Dim bulan As String = time.ToString("MM")
            Dim periode_open As String = time.AddMonths(1).ToString("ddMMyyyy")
            Dim periode_close As String = time.AddMonths(2).AddDays(-5).ToString("ddMMyyyy")

            Dim string_bulan As String = get_string_bulan(bulan)

            Dim NO_PELANGGAN As String = ""

            Using wr As StreamWriter = New StreamWriter(save_file_txt)

                Do While row.Read()

                    wr.WriteLine(
                        ("0239" + row("NO_REKENING").ToString + row("NO_PELANGGAN").ToString).PadRight(33) +
                        periode_open +
                        periode_close +
                        "        0000000000000000000000000000                " +
                        row("JUMLAH_BAYAR").ToString.PadLeft(12, "0")
                    )

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
            File.WriteAllText(respon_txt, msg_respon)

            'Console.ReadLine()
            Environment.Exit(0)

        End Try

    End Sub

End Module
