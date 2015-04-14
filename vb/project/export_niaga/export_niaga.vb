Imports System.Data.SqlClient
Imports System.IO
Imports System.Globalization

Module export_niaga

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
        'app_path = "F:\UwAmp\www\pkb\vb\export\niaga"

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
                "SUM( (ISNULL(b.JUMLAH_AIR,0) + ISNULL(b.ABONEMEN,0) + ISNULL(b.DENDA,0) + ISNULL(b.JUMLAH_IPL,0)) - (ISNULL(DISKON_RUPIAH_AIR,0) + ISNULL(DISKON_RUPIAH_IPL,0)) ) AS JUMLAH_BAYAR " +
            "FROM " +
                "KWT_PEMBAYARAN_AI b " +
            "WHERE " +
                "b.STATUS_BAYAR IS NULL " +
            "GROUP BY b.NO_PELANGGAN " +
            "ORDER BY b.NO_PELANGGAN "

            row = cmd.ExecuteReader()

            ' ============== CHECK FILE ==============

            Dim save_file_11_txt As String = app_path + "\files\11D_NIAGA_EXPORT_" + periode + ".txt"
            Dim save_file_12_txt As String = app_path + "\files\12D_NIAGA_EXPORT_" + periode + ".txt"

            If File.Exists(save_file_11_txt) = False Then
                File.AppendAllText(save_file_11_txt, "")
            Else
                File.WriteAllText(save_file_11_txt, "")
            End If

            If File.Exists(save_file_12_txt) = False Then
                File.AppendAllText(save_file_12_txt, "")
            Else
                File.WriteAllText(save_file_12_txt, "")
            End If

            ' ============== FILL DATA ==============

            Dim bulan As String = time.ToString("MM")
            Dim tahun As String = time.ToString("yyyy")
            Dim periode_close As String = time.AddMonths(2).AddDays(-3).ToString("ddMMyy")
            Dim string_bulan As String = get_string_bulan(bulan)

            Dim NO_PELANGGAN As String = ""

            Using wr11 As StreamWriter = New StreamWriter(save_file_11_txt)
                Using wr12 As StreamWriter = New StreamWriter(save_file_12_txt)

                    Do While row.Read()

                        NO_PELANGGAN = row("NO_PELANGGAN").ToString

                        If NO_PELANGGAN.Length > 11 Then
                            wr12.WriteLine(
                                "9769" +
                                NO_PELANGGAN +
                                periode_close +
                                row("JUMLAH_BAYAR").ToString.PadLeft(15) + "00" +
                                "TAGIHAN AIR DAN IPL sd " + string_bulan + " " + tahun
                                )
                        Else
                            wr11.WriteLine(
                                "9769" +
                                NO_PELANGGAN +
                                periode_close +
                                row("JUMLAH_BAYAR").ToString.PadLeft(15) + "00" +
                                "TAGIHAN AIR DAN IPL sd " + string_bulan + " " + tahun
                                )
                        End If

                    Loop

                    row.Close()

                End Using
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
